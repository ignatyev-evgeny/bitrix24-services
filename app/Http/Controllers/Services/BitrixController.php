<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\DepartmentsModel;
use App\Models\Bitrix\PortalsModel;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class BitrixController extends Controller {
    public function install(Request $request) {
        try {
            if(empty($request['DOMAIN'])) throw new Exception(__('Ошибка при получении домена портала'));
            if(empty($request['AUTH_ID'])) throw new Exception(__('Ошибка при получении ключа аутентификации'));
            $data = [
                'DOMAIN' => $request['DOMAIN'],
                'AUTH_ID' => $request['AUTH_ID'],
                'MEMBER_ID' => $request['member_id'],
                'PORTAL_ID' => self::createPortal($request['DOMAIN'])['id'] ?? null,
                'REQUEST' => $request->all(),
            ];
            if(empty($data['PORTAL_ID'])) throw new Exception(__('Ошибка создании портала'));
            self::loadDepartments($data);
            self::loadUsers($data);
            $currentMember = self::getCurrentMember($data);
            if(empty($currentMember['member']['response']['ID']) || empty($currentMember['member']['local']['id'])) throw new Exception(__('Ошибка при получении или создании текущего пользователя'));
            $data['USER'] = $currentMember;
            $data['ADMIN'] = self::checkIsAdmin($data, $currentMember)['isAdmin'] ?? false;
            Cache::put($data['MEMBER_ID'], User::find($currentMember['member']['local']['id']), now()->addMinutes((int) config('session.lifetime')));
            return redirect()->route('home', [
                'member_id' => $data['MEMBER_ID']
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort(403, $exception->getMessage());
        }
    }
    private function createPortal(string $domain) {
        try {
            DB::beginTransaction();
            $newPortal = PortalsModel::firstOrCreate([
                'domain' => $domain
            ]);
            DB::commit();
            return $newPortal->toArray();
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return null;
        }
    }
    private function loadDepartments(array $data) {
        try {
            DB::beginTransaction();
            $departments = self::sendRequest($data, 'department.get');
            if(empty($departments['result'])) throw new Exception(__('Ошибка при получении списка подразделений'));
            foreach ($departments['result'] as $department) {
                if(!DepartmentsModel::whereBitrixId($department['ID'])->wherePortal($data['PORTAL_ID'])->exists()) {
                    $departmentData[] = [
                        'bitrix_id' => $department['ID'],
                        'portal' => $data['PORTAL_ID'],
                        'name' => $department['NAME']
                    ];
                }
            }
            if(!empty($departmentData)) DepartmentsModel::insert($departmentData);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
    }
    private function loadUsers(array $data) {
        try {
            DB::beginTransaction();
            $users = self::sendRequest($data, 'user.get');
            if(empty($users['result'])) throw new Exception(__('Ошибка при получении списка пользователей'));
            foreach ($users['result'] as $user) {
                if(!User::whereBitrixId($user['ID'])->wherePortal($data['PORTAL_ID'])->exists()) {
                    $firstName = !empty($user['NAME']) ? $user['NAME'] : '';
                    $lastName = !empty($user['LAST_NAME']) ? $user['LAST_NAME'] : '';
                    $email = !empty($user['EMAIL']) ? $user['EMAIL'] : null;
                    $timestamp = Carbon::now();
                    $userData[] = [
                        'portal' => $data['PORTAL_ID'],
                        'bitrix_id' => $user['ID'],
                        'name' => $firstName.' '.$lastName,
                        'email' => $email,
                        'photo' => !empty($user['PERSONAL_PHOTO']) ? $user['PERSONAL_PHOTO'] : null,
                        'phone_personal' => !empty($user['PERSONAL_MOBILE']) ? $user['PERSONAL_MOBILE'] : null,
                        'phone_work' => !empty($user['WORK_PHONE']) ? $user['WORK_PHONE'] : null,
                        'departments' => json_encode($user['UF_DEPARTMENT']) ?? null,
                        'position' => !empty($user['WORK_POSITION']) ? $user['WORK_POSITION'] : null,
                        'lang' => $user['LANG'] ?? 'RU',
                        'password' => Hash::make($email),
                        'user' => json_encode($user),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }
            }
            if(!empty($userData)) User::insert($userData);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }



    }
    private function getCurrentMember(array $data) {
        try {
            DB::beginTransaction();
            $currentMember = self::sendRequest($data, 'user.current');
            if(empty($currentMember['result']['ID'])) throw new Exception(__('Ошибка при получении информации по текущему пользователю'));
            $currentMember = $currentMember['result'];
            $localUser = User::whereBitrixId($currentMember['ID'])->first();
            if(empty($localUser)) throw new Exception(__('Пользователь не найден локально'));
            $localUser->auth_id = $data['REQUEST']['AUTH_ID'];
            $localUser->refresh_id = $data['REQUEST']['REFRESH_ID'];
            $localUser->member_id = $data['REQUEST']['member_id'];
            $localUser->auth = $data['REQUEST'];
            $localUser->save();
            DB::commit();
            return [
                'status' => true,
                'member' => [
                    'response' => $currentMember,
                    'local' => $localUser->toArray()
                ]
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return null;
        }
    }
    private function checkIsAdmin(array $data, array $currentMember) {
        try {
            DB::beginTransaction();
            $isAdmin = self::sendRequest($data, 'user.admin');
            if(!isset($isAdmin['result'])) throw new Exception(__('Ошибка при получении информации по текущему пользователю'));
            User::find($currentMember['member']['local']['id'])->update([
                'is_admin' => true
            ]);
            DB::commit();
            return [
                'status' => true,
                'isAdmin' => $isAdmin['result']
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }


    }
    private function sendRequest(array $data, string $endpoint) {
        try {
            $start = 0;
            $response['result'] = $response['time'] = [];
            do {
                $currentResponse = Http::get("https://{$data['DOMAIN']}/rest/$endpoint.json?auth={$data['AUTH_ID']}&start=".$start)->json();
                $fullResponse[] = $currentResponse;
                $start = !empty($currentResponse['next']) ? $currentResponse['next'] : 0;
            } while (!empty($currentResponse['next']) && is_array($currentResponse['result']));
            if(is_array($currentResponse['result'])) {
                foreach ($fullResponse as $oneResponse) {
                    $response['result'] = array_merge($response['result'], $oneResponse['result']);
                    $response['time'] = $oneResponse['time'];
                }
                return $response;
            }
            return $currentResponse;
        } catch (Exception $exception) {
            report($exception);
            return false;
        }
    }
}
