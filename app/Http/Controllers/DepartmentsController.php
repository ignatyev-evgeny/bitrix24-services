<?php

namespace App\Http\Controllers;

use App\Models\Bitrix\DepartmentsModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class DepartmentsController extends Controller {
    public function getDepartments($memberId) {
        try {
            $auth = Cache::get($memberId);
            return view('certification.departments.list', [
                'departments' => DepartmentsModel::where('portal', $auth->portal)->paginate(25),
                'managers' => User::with('portalObject')->where('portal', $auth->portal)->get(),
                'auth' => Cache::get($memberId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function setManagers(Request $request, $memberId) {
        try {
            $auth = Cache::get($memberId);
            $department = DepartmentsModel::find($request->id);
            if(empty($department)) throw new Exception(__('Подразделение не найдено'), 404);
            if($department->portal != $auth->portal) throw new Exception(__('Подразделение которое вы пытаетесь отредактировать не относится к вашему порталу'), 403);
            $department->managers = $request->managers;
            $department->save();
            return response()->json([
                'success' => true,
                'message' => __('Менеджеры подразделения были успешно отредактированы')
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
}
