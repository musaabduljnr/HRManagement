<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RedirectIfNotInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $request->path();

        // Allow install paths to bypass
        if ($path === 'install' || strpos($path, 'install/') === 0) {
            if ($this->isInstalled()) {
                return redirect('/');
            }
            return $next($request);
        }

        // Redirect to install if not installed
        if (!$this->isInstalled()) {
            return redirect()->route('install.index');
        }

        return $next($request);
    }

    /**
     * Check if the application is installed.
     *
     * @return bool
     */
    protected function isInstalled()
    {
        if (app()->environment('testing')) {
            return true;
        }

        try {
            // Check if the system_settings table exists
            if (!Schema::hasTable('system_settings')) {
                return false;
            }

            // Retrieve the installation status key
            $installed = DB::table('system_settings')
                ->where('key', 'app_installed')
                ->value('value');

            return $installed === 'true';
        } catch (\Exception $e) {
            // If database connection fails, it is not installed
            return false;
        }
    }
}
