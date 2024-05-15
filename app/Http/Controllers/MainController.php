<?php

namespace App\Http\Controllers;

use App\Models\Bitrix\DepartmentsModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller {
    public function home($authId) {
        try {
            return view('certification.index', [
                'auth' => Cache::get($authId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function login() {
        return view('login.login');
    }

    public function getTests() {
        return view('certification.tests.list', [
            'tests' => DepartmentsModel::where('portal', Auth::user()->portal)->get()->toArray()
        ]);
    }
    public function getCertifications() {
        return view('certification.certifications.list', [
            'certifications' => DepartmentsModel::where('portal', Auth::user()->portal)->get()->toArray()
        ]);
    }
}
