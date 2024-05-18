<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersGroups;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UsersGroupsController extends Controller {
    public function list($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.usersGroups.list', [
                'usersGroups' => UsersGroups::where('portal', $auth->portal)->paginate(10),
                'auth' => Cache::get($authId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
    public function create($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.usersGroups.create', [
                'auth' => $auth,
                'users' => User::where('portal', $auth->portal)->whereJsonContains('departments', $auth->departments)->get(),
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
    public function store(Request $request) {
        $data = $request->validate([
            'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
            'title' => ['required', 'string'],
            'authId' => ['required', 'string', 'exists:users,auth_id'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'users' => ['required', 'array']
        ]);
        $auth = Cache::get($data['authId']);
        $data['manager'] = $auth->id;
        try {
            DB::beginTransaction();
            UsersGroups::create($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('Группа сотрудников успешно добавлена')
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function show(UsersGroups $group, $authId) {
        $auth = Cache::get($authId);
        try {
            if(empty($group)) throw new Exception(__("Выбранная группа сотрудников не существует"));
            if($auth->portal != $group->portal) throw new Exception(__("у Вас нет доступа для редактирования этой группы сотрудников"));
            return view('certification.usersGroups.show', [
                'auth' => Cache::get($authId),
                'group' => $group,
                'groupUsers' => $group->users,
                'users' => User::where('portal', $auth->portal)->whereJsonContains('departments', $auth->departments)->get(),
            ]);
        } catch (Exception $exception) {
            report($exception);
            return response()->view('errorPage', [
                'message' => $exception->getMessage(),
                'auth' => $auth
            ], 500);
        }
    }
    public function update(Request $request, UsersGroups $group) {
        $data = $request->validate([
            'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
            'title' => ['required', 'string'],
            'authId' => ['required', 'string', 'exists:users,auth_id'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'users' => ['required', 'array']
        ]);
        $auth = Cache::get($data['authId']);;
        try {
            if($auth->portal != $group->portal) throw new Exception(__("Нет доступа для редактирования данной группы сотрудников"));
            if($auth->id != $group->manager || empty(array_intersect($auth->departments, $group->manager_info->departments))) throw new Exception(__("Нет доступа для редактирования данной группы сотрудников"));
            DB::beginTransaction();
            $group->update($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('Группа сотрудников успешно обновлена')
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function destroy(UsersGroups $userGroups) {
        $userGroups->delete();

        return response()->json();
    }
}
