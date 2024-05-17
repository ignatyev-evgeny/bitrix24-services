<?php

namespace App\Http\Controllers;

use App\Models\Bitrix\DepartmentsModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class DepartmentsController extends Controller {
    public function getDepartments($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.departments.list', [
                'departments' => DepartmentsModel::where('portal', $auth->portal)->get(),
                'managers' => User::with('portalObject')->where('portal', $auth->portal)->get(),
                'auth' => Cache::get($authId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function setManagers(Request $request, $authId) {

        $data = $request->validate([
            'departmentId' => ['required', 'string', 'exists:bitrix_departments,id'],
            'userId' => ['required', 'string', 'exists:users,id'],
            'event' => ['required', 'string', Rule::in(['select', 'unselect'])],
        ]);

        try {
            $auth = Cache::get($authId);
            $department = DepartmentsModel::find($data['departmentId']);
            if(empty($department)) throw new Exception(__('Подразделение не найдено'), 404);
            if($department->portal != $auth->portal) throw new Exception(__('Подразделение которое вы пытаетесь отредактировать не относится к вашему порталу'), 403);
            $managers = empty($department->managers) ? [] : $department->managers;
            if($data['event'] == 'select') $managers[] = $data['userId'];
            if($data['event'] == 'unselect') $managers = unsetByValue($managers, $data['userId']);
            $department->managers = numericKeyToIntArr($managers);
            $department->save();
            return response()->json([
                'success' => true,
                'message' => __('Менеджеры подразделения были успешно отредактированы')
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            report($exception);
            return response()->json([
                'success' => false,
                'message' => __('Ошибка при обновлении менеджеров подразделения')
            ], Response::HTTP_OK);
        }
    }
}
