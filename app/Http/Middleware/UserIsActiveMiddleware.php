<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserIsActiveMiddleware {
    public function handle(Request $request, Closure $next) {
        try {
            $auth = Cache::get($request->route()->parameter('auth_id'));
            if(empty($auth)) return redirect()->route('login');
            if(!$auth->active && !$auth->is_admin) return response()->view('errorAccess');
            return $next($request);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
}
