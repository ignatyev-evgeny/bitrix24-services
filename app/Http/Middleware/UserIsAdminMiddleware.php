<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserIsAdminMiddleware {
    public function handle(Request $request, Closure $next) {
        try {
            $auth = Cache::get($request->route()->parameter('member_id'));
            if(!$auth->is_admin) abort(403, __('Доступ запрещен. Пользователь не является администратором'));
            return $next($request);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
}
