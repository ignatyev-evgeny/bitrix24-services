<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\DepartmentsModel;
use App\Models\Bitrix\MembersModels;
use App\Models\Bitrix\PortalsModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\Concerns\Has;

class BitrixController extends Controller {

    public function install(Request $request) {
        try {
            if(empty($request['DOMAIN'])) throw new Exception(__('Ошибка при получении домена портала'));
            if(empty($request['AUTH_ID'])) throw new Exception(__('Ошибка при получении ключа аутентификации'));
            $data = [
                'DOMAIN' => $request['DOMAIN'],
                'AUTH_ID' => $request['AUTH_ID'],
                'MEMBER_ID' => $request['member_id'],
                'PORTAL_ID' => self::createPortal($request['DOMAIN'])['portal']['id'] ?? null,
                'REQUEST' => $request->all(),
            ];
            if(empty($data['PORTAL_ID'])) throw new Exception(__('Ошибка создании портала'));
            $currentMember = self::getCurrentMember($data);
            if(empty($currentMember['member']['response']['result']['ID']) || empty($currentMember['member']['local']['id'])) throw new Exception(__('Ошибка при получении или создании текущего пользователя'));
            $data['USER'] = $currentMember;
            $data['ADMIN'] = self::checkIsAdmin($data, $currentMember)['isAdmin'] ?? false;
            return redirect()->route('services.bitrix.certification.home', [
                'member_id' => $data['MEMBER_ID']
            ]);
        } catch (Exception $exception) {
            abort(403, $exception->getMessage());
        }
    }
    public function home() {
        return view('services.bitrix.certification.index');
    }

    public function getUsers() {
        return view('services.bitrix.certification.users.list', [
            'users' => User::where('portal', Auth::user()->portal)->get()->toArray()
        ]);
    }

    public function getDepartments() {
        return view('services.bitrix.certification.departments.list', [
            'departments' => DepartmentsModel::where('portal', Auth::user()->portal)->get()->toArray()
        ]);
    }

    public function getTests() {
        return view('services.bitrix.certification.tests.list', [
            'tests' => DepartmentsModel::where('portal', Auth::user()->portal)->get()->toArray()
        ]);
    }

    public function getCertifications() {
        return view('services.bitrix.certification.certifications.list', [
            'certifications' => DepartmentsModel::where('portal', Auth::user()->portal)->get()->toArray()
        ]);
    }

    private function createPortal(string $domain) {
        try {
            DB::beginTransaction();
            $newPortal = PortalsModel::firstOrCreate([
                'domain' => $domain
            ]);
            DB::commit();
            return [
                'status' => true,
                'portal' => $newPortal->toArray()
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => __('Ошибка при добавлении портала')
            ];
        }
    }
    private function createDepartments(object $portal, array $data) {
        try {
            DB::beginTransaction();
            $departments = Http::get("https://{$data['DOMAIN']}/rest/department.get.json?auth={$data['AUTH_ID']}")->json();

            if(empty($departments['result'])) {
                return [
                    'status' => false,
                    'message' => __('Ошибка при получении списка подразделений')
                ];
            }

            foreach ($departments['result'] as $department) {
                DepartmentsModel::updateOrCreate([
                    'bitrix_id' => $department['ID'],
                    'portal' => $portal->id
                ], [
                    'name' => $department['NAME'],
                    'parent' => $department['PARENT'] ?? null,
                ]);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => __('Список подразделений был успешно получен и обновлен')
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => __('Ошибка при обновлении списка подразделений')
            ];
        }



    }

    private function createMember(object $portal, $request, array $member, string $type = null) {
        try {
            return MembersModels::updateOrCreate(
                [
                    'portal' => $portal->id,
                    'bitrix_id' => $member['ID'],
                ],
                [
                    'member_id' => $type != 'listMembers' && !empty($request['member_id']) ? $request['member_id'] : null,
                    'auth_id' => $type != 'listMembers' && !empty($request['AUTH_ID']) ? $request['AUTH_ID'] : null,
                    'refresh_id' => $type != 'listMembers' && !empty($request['REFRESH_ID']) ? $request['REFRESH_ID'] : null,
                    'active' => $member['ACTIVE'] ?? false,
                    'name' => $member['NAME'],
                    'lastname' => $member['LAST_NAME'],
                    'photo' => $member['PERSONAL_PHOTO'] ?? null,
                    'email' => $member['EMAIL'],
                    'last_login' => $member['LAST_LOGIN'],
                    'date_register' => $member['DATE_REGISTER'],
                    'is_online' => $member['IS_ONLINE'],
                    'time_zone_offset' => $member['TIME_ZONE_OFFSET'] ?? null,
                    'timestamp_x' => empty($member['TIMESTAMP_X']) ? null : $member['TIMESTAMP_X'],
                    'last_activity_date' => empty($member['LAST_ACTIVITY_DATE']) ? null : $member['LAST_ACTIVITY_DATE'],
                    'personal_gender' => $member['PERSONAL_GENDER'] ?? null,
                    'personal_birthday' => $member['PERSONAL_BIRTHDAY'] ?? null,
                    'user_type' => $member['USER_TYPE'] ?? null,
                    'uf_department' => $member['UF_DEPARTMENT'],
                    'lang' => $request['LANG'],
                    'auth' => $request->all(),
                    'member' => $member
                ]
            );
        } catch (Exception $exception) {
            dd($exception);
        }
    }

    private function createMembers(object $portal, array $data) {
        $members = Http::get("https://{$data['DOMAIN']}/rest/user.get.json?auth={$data['AUTH_ID']}")->json();
        if(empty($members['result'])) throw new Exception(__('Ошибка при получении списка пользователей'));

        foreach ($members as $member) {
            self::createMember($portal, $request, $member, 'listMembers');
        }
    }

    private function getCurrentMember(array $data) {
        try {
            DB::beginTransaction();
            $currentMember = Http::get("https://{$data['DOMAIN']}/rest/user.current.json?auth={$data['AUTH_ID']}")->json();
            if(empty($currentMember['result']['ID'])) throw new Exception(__('Ошибка при получении информации по текущему пользователю'));

            $localMember = User::updateOrCreate(
                [
                    'portal' => $data['PORTAL_ID'],
                    'bitrix_id' => $currentMember['result']['ID'],
                ],
                [
                    'member_id' => $data['REQUEST']['member_id'],
                    'auth_id' => $data['REQUEST']['AUTH_ID'],
                    'refresh_id' => $data['REQUEST']['REFRESH_ID'],
                    'active' => $currentMember['result']['ACTIVE'],
                    'name' => $currentMember['result']['NAME'],
                    'lastname' => $currentMember['result']['LAST_NAME'],
                    'photo' => $currentMember['result']['PERSONAL_PHOTO'],
                    'email' => $currentMember['result']['EMAIL'],
                    'password' => Hash::make($currentMember['result']['EMAIL']),
                    'last_login' => $currentMember['result']['LAST_LOGIN'],
                    'date_register' => $currentMember['result']['DATE_REGISTER'],
                    'is_online' => $currentMember['result']['IS_ONLINE'],
                    'time_zone_offset' => $currentMember['result']['TIME_ZONE_OFFSET'],
                    'timestamp_x' => $currentMember['result']['TIMESTAMP_X'],
                    'last_activity_date' => $currentMember['result']['LAST_ACTIVITY_DATE'],
                    'personal_gender' => $currentMember['result']['PERSONAL_GENDER'] ?? null,
                    'personal_birthday' => $currentMember['result']['PERSONAL_BIRTHDAY'] ?? null,
                    'user_type' => $currentMember['result']['USER_TYPE'] ?? null,
                    'uf_department' => $currentMember['result']['UF_DEPARTMENT'],
                    'member' => $currentMember,
                    'auth' => $data['REQUEST'],
                ]
            );

            DB::commit();

            return [
                'status' => true,
                'member' => [
                    'response' => $currentMember,
                    'local' => $localMember->toArray()
                ]
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => __('Ошибка при получении текущего пользователя')
            ];
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
                'message' => __('Ошибка при проверке пользователя на статус администратора')
            ];
        }


    }

    private function sendRequest(array $data, string $endpoint) {
        try {
            return Http::get("https://{$data['DOMAIN']}/rest/{$endpoint}.json?auth={$data['AUTH_ID']}")->json();
        } catch (Exception $exception) {
            report($exception);
            return false;
        }
    }
}
