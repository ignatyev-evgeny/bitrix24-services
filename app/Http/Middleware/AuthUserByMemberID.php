<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthUserByMemberID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        try {
            $user = User::where('member_id', $request->route()->parameter('member_id'))->first();
            if(empty($user)) throw new Exception(__('Пользователь не найден'));
            Auth::loginUsingId($user->id);
            return $next($request);
        } catch (Exception $exception) {
            abort(403, $exception->getMessage());
        }
    }
}
