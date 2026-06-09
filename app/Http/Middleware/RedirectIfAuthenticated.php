<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (in_array(Auth::user()->role, [User::USER_ROLE_ADMIN, User::USER_ROLE_HR_MANAGER, User::USER_ROLE_PAYROLL_MANAGER, User::USER_ROLE_DEPT_MANAGER])) {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/employee');
            }
        }

        return $next($request);
    }
}
