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
use Illuminate\Support\Facades\Log;

class BitrixController extends Controller {
    // https://monkey.bitrix24.ru/oauth/authorize/?client_id=local.6621555f3ead38.99934649
    public function install(Request $request) {
        try {
            self::log("Request from Bitrix24 -> ".collect($request->toArray())->toJson());
            $data = empty($request->code) ? self::authUserIframe($request->all()) : self::authUseroAuth($request->all());
            if(empty($data)) throw new Exception('Ошибка авторизации');
            self::loadDepartments($data);
            self::loadUsers($data);
            $currentMember = self::getCurrentMember($data);
            if(empty($currentMember['member']['response']['ID']) || empty($currentMember['member']['local']['id'])) throw new Exception(__('Ошибка при получении или создании текущего пользователя'));
            $data['USER'] = $currentMember;
            $data['ADMIN'] = self::checkIsAdmin($data, $currentMember)['isAdmin'] ?? false;
            Cache::put($data['AUTH_ID'], User::find($currentMember['member']['local']['id']), now()->addMinutes((int) config('session.lifetime')));
            return redirect()->route('home', [
                'auth_id' => $data['AUTH_ID']
            ]);
        } catch (Exception $exception) {
            report($exception);
            return view('errorAuth');
        }
    }

    private function authUserIframe(array $request): array {
        try {
            if(empty($request['DOMAIN'])) throw new Exception(__('Ошибка при получении домена портала'));
            if(empty($request['AUTH_ID'])) throw new Exception(__('Ошибка при получении ключа аутентификации'));
            DB::beginTransaction();
            $data = [
                'DOMAIN' => $request['DOMAIN'],
                'AUTH_ID' => $request['AUTH_ID'],
                'REFRESH_ID' => $request['REFRESH_ID'],
                'MEMBER_ID' => $request['member_id'],
                'PORTAL_ID' => self::createPortal($request['DOMAIN'])['id'] ?? null,
                'REQUEST' => $request,
            ];
            DB::commit();
            if(empty($data['PORTAL_ID'])) throw new Exception(__('Ошибка создании портала'));
            return $data;
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return [];
        }
    }
    private function authUseroAuth(array $request): array {
        try {
            if(empty($request['code'])) throw new Exception(__('Ошибка oAuth авторизации пользователя, не передан -> code'));
            if(empty($request['domain'])) throw new Exception(__('Ошибка oAuth авторизации пользователя, не передан -> domain'));
            DB::beginTransaction();
            $oAuthCheck = Http::get("https://oauth.bitrix.info/oauth/token/", [
                'grant_type' => 'authorization_code',
                'client_id' => 'local.6621555f3ead38.99934649',
                'client_secret' => 'mTdpb8wDUdn7UPEWxuFZ9HCXaSdhv4n9wqd78TkCM5GIwy4J6a',
                'code' => $request['code'],
            ])->json();
            $exceptionMessage = !empty($oAuthCheck['error_description']) ? $oAuthCheck['error_description'] : __('Ошибка oAuth авторизации пользователя, не передан -> access_token');
            if(empty($oAuthCheck['access_token'])) throw new Exception($exceptionMessage);
            $data = [
                'DOMAIN' => $request['domain'],
                'AUTH_ID' => $oAuthCheck['access_token'],
                'REFRESH_ID' => $oAuthCheck['refresh_token'],
                'MEMBER_ID' => $oAuthCheck['member_id'],
                'PORTAL_ID' => self::createPortal($request['domain'])['id'] ?? null,
                'REQUEST' => [
                    'request' => $request,
                    'oAuth' => $oAuthCheck
                ],
            ];
            DB::commit();
            if(empty($data['PORTAL_ID'])) throw new Exception(__('Ошибка создании портала'));
            return $data;
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return [];
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
    private function getCurrentMember(array $data): array {
        try {
            DB::beginTransaction();
            $currentMember = self::sendRequest($data, 'user.current');
            if(empty($currentMember['result']['ID'])) throw new Exception(__('Ошибка при получении информации по текущему пользователю'));
            $currentMember = $currentMember['result'];
            $localUser = User::whereBitrixId($currentMember['ID'])->first();
            if(empty($localUser)) throw new Exception(__('Пользователь не найден локально'));
            $localUser->auth_id = $data['AUTH_ID'];
            $localUser->refresh_id = $data['REFRESH_ID'];
            $localUser->member_id = $data['MEMBER_ID'];
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
            return [];
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
                //self::log("Request URL [{$endpoint}] -> https://{$data['DOMAIN']}/rest/$endpoint.json?auth={$data['AUTH_ID']}&start=".$start);
                $currentResponse = Http::get("https://{$data['DOMAIN']}/rest/$endpoint.json?auth={$data['AUTH_ID']}&start=".$start)->json();
                //self::log("Request Response [{$endpoint}] -> ".json_encode($currentResponse).PHP_EOL);
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

    private function log($message) {
        Log::channel('bitrixRequest')->debug($message);
    }
}
