<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserIsAdminOrSupportMiddleware {
    public function handle(Request $request, Closure $next) {
        try {
            $auth = Cache::get($request->route()->parameter('member_id'));
            if(empty($auth)) return response()->view('errorAuth');
            if(!$auth->is_admin && !$auth->is_support) return response()->view('errorAccess');
            return $next($request);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
}
