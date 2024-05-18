<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller {
    public function getUsers($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.users.list', [
                'users' => User::with('portalObject')->where('portal', $auth->portal)->get(),
                'auth' => $auth,
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
    public function updateActive(Request $request, $authId) {
        try {
            $auth = Cache::get($authId);
            if(!isset($request->currentStatus) || !in_array($request->currentStatus, [0, 1])) throw new Exception(__('Новый статус не передан. Или переданный статус не является разрешенным'), 403);
            $user = User::find($request->userID);
            if(empty($user)) throw new Exception(__('Пользователь не найден'), 404);


            if($user->portal != $auth->portal) throw new Exception(__('Пользователь которого вы пытаетесь отредактировать не относится к вашему порталу'), 403);
            $newStatus = match ($request->currentStatus) {
                "0" => 1,
                "1" => 0
            };
            $user->active = $newStatus;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => __('Статус пользователя был успешно изменен.')
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
    public function updateIsSupport(Request $request, $authId) {
        try {
            $auth = Cache::get($authId);
            if(!isset($request->isSupportStatus) || !in_array($request->isSupportStatus, [0, 1])) throw new Exception(__('Статус роли пользователя не передан. Или переданный статус роли не является разрешенным'), 403);
            $user = User::find($request->userID);
            if(empty($user)) throw new Exception(__('Пользователь не найден'), 404);
            if($user->portal != $auth->portal) throw new Exception(__('Пользователь которого вы пытаетесь отредактировать не относится к вашему порталу'), 403);
            $newSupportStatus = match ($request->isSupportStatus) {
                "0" => 1,
                "1" => 0
            };
            $user->is_support = $newSupportStatus;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => __('Статус роли пользователя был успешно изменен.')
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
}
