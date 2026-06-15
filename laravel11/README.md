# Laravel 11 Skeleton

This directory holds a fresh Laravel 11 skeleton created for migration planning.

## Purpose

- Preserve the existing Laravel 5 project in `hrm/`
- Provide a separate Laravel 11 app structure under `hrm/laravel11`
- Allow manual migration of controllers, models, routes, views, and configs

## Next steps

1. Install dependencies with Composer in `laravel11`.
2. Copy legacy app logic from `hrm/app` to `hrm/laravel11/app` incrementally.
3. Migrate routes into `hrm/laravel11/routes` and service providers.
4. Replace legacy helpers and old middleware patterns with Laravel 11 equivalents.
