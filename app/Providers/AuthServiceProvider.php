<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-payroll', function($user) {
            return in_array($user->role, [\App\User::USER_ROLE_ADMIN, \App\User::USER_ROLE_PAYROLL_MANAGER]);
        });

        Gate::define('manage-recruitment', function($user) {
            return in_array($user->role, [\App\User::USER_ROLE_ADMIN, \App\User::USER_ROLE_HR_MANAGER]);
        });

        Gate::define('manage-employees', function($user) {
            return in_array($user->role, [\App\User::USER_ROLE_ADMIN, \App\User::USER_ROLE_HR_MANAGER, \App\User::USER_ROLE_DEPT_MANAGER]);
        });

        Gate::define('manage-leaves', function($user) {
            return in_array($user->role, [\App\User::USER_ROLE_ADMIN, \App\User::USER_ROLE_HR_MANAGER, \App\User::USER_ROLE_DEPT_MANAGER]);
        });

        Gate::define('manage-settings', function($user) {
            return in_array($user->role, [\App\User::USER_ROLE_ADMIN, \App\User::USER_ROLE_HR_MANAGER]);
        });

        Gate::define('use-assistant', function($user) {
            return in_array($user->role, [
                \App\User::USER_ROLE_ADMIN, 
                \App\User::USER_ROLE_EMPLOYEE, 
                \App\User::USER_ROLE_HR_MANAGER, 
                \App\User::USER_ROLE_PAYROLL_MANAGER, 
                \App\User::USER_ROLE_DEPT_MANAGER
            ]);
        });
    }
}
