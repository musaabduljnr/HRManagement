<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\User;
use Validator;

class InstallController extends Controller
{
    public function index()
    {
        $step = session('install_step', 1);
        
        // Prevent skipped steps
        if ($step < 1 || $step > 5) {
            $step = 1;
            session(['install_step' => 1]);
        }

        // Requirements for Step 1
        $requirements = [];
        if ($step == 1) {
            $requirements = [
                'php_version' => version_compare(PHP_VERSION, '7.0.0', '>='),
                'openssl' => extension_loaded('openssl'),
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
                'gd' => extension_loaded('gd'),
                'zip' => extension_loaded('zip'),
                'storage_writable' => is_writable(storage_path()),
                'cache_writable' => is_writable(base_path('bootstrap/cache')),
            ];
        }

        // Database status for Step 2
        $dbConfig = [];
        $dbError = null;
        $dbConnected = false;
        if ($step == 2) {
            $dbConfig = [
                'connection' => config('database.default'),
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => config('database.connections.mysql.database'),
                'username' => config('database.connections.mysql.username'),
            ];

            try {
                DB::connection()->getPdo();
                $dbConnected = true;
            } catch (\Exception $e) {
                $dbConnected = false;
                $dbError = $e->getMessage();
            }
        }

        // Check if admin user already exists for Step 4
        $adminExists = false;
        if ($step == 4) {
            try {
                $adminExists = User::where('role', User::USER_ROLE_ADMIN)->exists();
            } catch (\Exception $e) {
                $adminExists = false;
            }
        }

        return view('install.step' . $step, compact('requirements', 'dbConfig', 'dbConnected', 'dbError', 'adminExists'));
    }

    public function postStep(Request $request, $step)
    {
        switch ($step) {
            case 1:
                // Check all requirements
                $requirements = [
                    version_compare(PHP_VERSION, '7.0.0', '>='),
                    extension_loaded('openssl'),
                    extension_loaded('pdo'),
                    extension_loaded('mbstring'),
                    extension_loaded('tokenizer'),
                    extension_loaded('xml'),
                    extension_loaded('gd'),
                    extension_loaded('zip'),
                    is_writable(storage_path()),
                    is_writable(base_path('bootstrap/cache')),
                ];

                if (in_array(false, $requirements, true)) {
                    return back()->with('error', 'Server requirements not met.');
                }

                session(['install_step' => 2]);
                return redirect()->route('install.index');

            case 2:
                // Test database connection
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    return back()->with('error', 'Could not connect to the database. Error: ' . $e->getMessage());
                }

                session(['install_step' => 3]);
                return redirect()->route('install.index');

            case 3:
                // Run migrations and seeds
                try {
                    // Force run migrations
                    Artisan::call('migrate', ['--force' => true]);
                    
                    // Run seeders if DatabaseSeeder exists
                    if (class_exists('DatabaseSeeder')) {
                        Artisan::call('db:seed', ['--force' => true]);
                    }
                } catch (\Exception $e) {
                    return back()->with('error', 'Migration failed. Error: ' . $e->getMessage());
                }

                session(['install_step' => 4]);
                return redirect()->route('install.index');

            case 4:
                // Check if admin already exists
                $adminExists = false;
                try {
                    $adminExists = User::where('role', User::USER_ROLE_ADMIN)->exists();
                } catch (\Exception $e) {}

                if (!$adminExists) {
                    // Create admin account
                    $validator = Validator::make($request->all(), [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|string|email|max:255|unique:users',
                        'password' => 'required|string|min:6|confirmed',
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }

                    try {
                        User::create([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'role' => User::USER_ROLE_ADMIN,
                            'gender' => 'm',
                            'birth_date' => '1990-01-01',
                        ]);
                    } catch (\Exception $e) {
                        return back()->with('error', 'Failed to create Admin user: ' . $e->getMessage());
                    }
                }

                // Mark as installed in database
                try {
                    DB::table('system_settings')->updateOrInsert(
                        ['key' => 'app_installed'],
                        ['value' => 'true', 'updated_at' => now()]
                    );
                } catch (\Exception $e) {
                    return back()->with('error', 'Failed to update installation flag in database: ' . $e->getMessage());
                }

                // Clear Laravel caches
                try {
                    Artisan::call('config:clear');
                    Artisan::call('cache:clear');
                } catch (\Exception $e) {}

                session(['install_step' => 5]);
                return redirect()->route('install.index');

            case 5:
                // Finish installation
                session()->forget('install_step');
                return redirect('/');
        }

        return redirect()->route('install.index');
    }
}
