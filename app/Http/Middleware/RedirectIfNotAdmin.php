<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class RedirectIfNotAdmin
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
        $allowedAdminRoles = [
            User::USER_ROLE_ADMIN,
            User::USER_ROLE_HR_MANAGER,
            User::USER_ROLE_PAYROLL_MANAGER,
            User::USER_ROLE_DEPT_MANAGER
        ];
        if (!in_array($request->user()->role, $allowedAdminRoles)) {
            return redirect()->back();
        }

        return $next($request);
    }
}
