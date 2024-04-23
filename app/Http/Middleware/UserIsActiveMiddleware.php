<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserIsActiveMiddleware {
    public function handle(Request $request, Closure $next) {
        try {
            $auth = Cache::get($request->route()->parameter('member_id'));
            if(!$auth->active && !$auth->is_admin) abort(403, __('Доступ запрещен. Пользователь не активирован'));
            return $next($request);
        } catch (\Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
}
