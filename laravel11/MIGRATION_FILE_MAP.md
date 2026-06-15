# Laravel 5 → Laravel 11 File Mapping

This file maps the old `hrm/` Laravel 5 structure to the new `hrm/laravel11/` skeleton.

## Core application files

- `hrm/app/Http/Controllers/` → `hrm/laravel11/app/Http/Controllers/`
- `hrm/app/Http/Middleware/` → `hrm/laravel11/app/Http/Middleware/`
- `hrm/app/Models/` and other models under `app/` → `hrm/laravel11/app/Models/`
- `hrm/app/Providers/` → `hrm/laravel11/app/Providers/`
- `hrm/app/Exceptions/` → `hrm/laravel11/app/Exceptions/`
- `hrm/app/Http/helpers.php` → `hrm/laravel11/app/Http/helpers.php`

## Route and view migration

- `hrm/routes/web.php` → `hrm/laravel11/routes/web.php`
- `hrm/routes/api.php` → `hrm/laravel11/routes/api.php`
- `hrm/resources/views/` → `hrm/laravel11/resources/views/`
- Package/module view namespaces such as `settings::index` should be migrated using `view()->addNamespace()` or dedicated package service providers.

## Configuration

- `hrm/config/*.php` → `hrm/laravel11/config/*.php`
- `hrm/bootstrap/app.php` → `hrm/laravel11/bootstrap/app.php`
- `hrm/public/index.php` → `hrm/laravel11/public/index.php`

## Database and factories

- `hrm/database/migrations/` → `hrm/laravel11/database/migrations/`
- `hrm/database/seeds/` → `hrm/laravel11/database/seeders/`
- `hrm/database/factories/` → `hrm/laravel11/database/factories/`

## Asset pipeline

- `hrm/resources/assets/` → `hrm/laravel11/resources/js/` and `hrm/laravel11/resources/css/`
- Add `vite.config.js` and `package.json` for modern asset builds.

## Important manual migration notes

- Replace Laravel 5 helper functions and facades with Laravel 11 equivalents.
- Convert `Route::group()` options to current middleware and named route syntax.
- Move package-specific service provider registration from `config/app.php` to `config/*.php` or new service providers.
- Migrate custom module autoloading: use PSR-4 and `composer.json` autoload rules instead of legacy classmap patterns.
