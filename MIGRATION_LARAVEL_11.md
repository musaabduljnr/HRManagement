# Laravel 11 Migration Preparation

## Project Overview

This is a Laravel HRM application currently built on Laravel 5.x conventions and structure.
The application includes custom modules for:
- Attendance and QR attendance
- Payroll
- Leave management
- Recruitment and reports
- Assets, dashboard, chat, discipline, time tracking

The codebase uses a modular structure under `app/Modules/` and retains legacy Laravel 5 controllers, middleware, auth scaffolding, and helper patterns.

## Current Project Structure

Key directories and files:

- `app/`
  - Core models: `User.php`, `Attendance.php`, `AttendanceQrToken.php`, `AttendanceRule.php`
  - Services: `AttendanceService.php`, `HrAssistantService.php`
  - HTTP controllers, middleware, auth controllers, helpers
- `app/Modules/`
  - `Attendance/`
  - `Pim/`
  - `Leave/`
  - `Recruitment/`
  - `Settings/`
  - `Time/`
  - `Assets/`
  - `Chat/`
  - `Dashboard/`
  - `Discipline/`
  - `Employee/`
- `routes/web.php`
- `resources/views/` and module-specific view folders under `app/Modules/*/resources/views`
- `database/migrations/`
- `database/factories/ModelFactory.php`
- `composer.json` and `composer.lock`

### Important files

- `app/Http/helpers.php` — global helpers autoloaded by Composer
- `app/Http/Kernel.php` — middleware definitions and groups
- `app/Providers/AuthServiceProvider.php` — Gate definitions and authorization logic
- `routes/web.php` — route definitions including auth, admin, employee, attendance, payroll, leave, recruitment, assets
- `composer.json` — current dependency definitions

## Current Dependencies

### Laravel / PHP
- `laravel/framework: 5.*`
- `php: ^8.2` (current composer setting; incompatible with actual legacy package versions)

### Primary packages
- `barryvdh/laravel-dompdf: ^0.7.1`
- `davejamesmiller/laravel-breadcrumbs: ^3.0`
- `doctrine/dbal: ^2.9`
- `guzzlehttp/guzzle: 6.*`
- `laravelcollective/html: ^5.3`
- `yajra/laravel-datatables-oracle: ~6.0`

### Development packages
- `fzaninotto/faker: ~1.4`
- `mockery/mockery: 0.9.*`
- `phpunit/phpunit: ~5.0`
- `symfony/css-selector: 3.1.*`
- `symfony/dom-crawler: 3.1.*`

### Notable legacy or abandoned packages
- `fzaninotto/faker` — abandoned
- `phpexcel` / `phpoffice/phpexcel` — abandoned, replaced by `phpoffice/phpspreadsheet`
- `guzzlehttp/guzzle 6.*` — legacy
- `doctrine/dbal 2.x` — outdated for modern Laravel
- old Yajra DataTables and Laravel Collective versions

## Database tables (inferred from migrations)

The current table set is derived from migration filenames and includes:

- `users`
- `password_resets`
- `social_media`
- `user_contact_details`
- `job_positions`
- `skills`
- `contract_types`
- `companies`
- `education_institutions`
- `user_jobs`
- `user_education`
- `user_experience`
- `user_skills`
- `user_documents`
- `languages`
- `user_languages`
- `salary_components`
- `user_salaries`
- `salary_salary_components`
- `leave_types`
- `holidays`
- `user_leaves`
- `user_leave_status`
- `user_preferences`
- `document_templates`
- `disciplinary_cases`
- `clients`
- `projects`
- `time_logs`
- `custom_fields`
- `dashboard_documents`
- `currencies`
- `salaries_config`
- `attendances`
- `attendance_rules`
- `employee_devices`
- `attendance_qr_tokens`
- `departments`
- `job_titles`
- `payroll_records`
- `payslips`
- `job_openings`
- `candidates`
- `interviews`
- `hr_policies`
- `activity_logs`
- `conversations`
- `messages`
- `assets` / asset-related tables created in asset module migrations

> Note: This list is inferred from migration file names and should be validated against the actual database schema before migration.

## Identified compatibility risks

### Code risks
- `str_random(...)` helper usage in application code and factory definitions
- legacy auth routes and controller traits
- old middleware syntax and route middleware registration
- global helper file usage
- hard-coded integer role/permission logic in `App/User` and `AuthServiceProvider`

### Dependency risks
- major package upgrades required for Laravel 11
- direct use of old 5.x-compatible packages
- `composer.json` PHP version mismatch with actual package ecosystem

### Migration risks
- database schema and migration compatibility with newer DB engines
- outdated migration style and foreign key conventions
- business logic reliant on legacy features that may change in Laravel 11

## Safety preparation checklist for Laravel 11 migration

### 1. Create a safe baseline
- [ ] commit or snapshot the current repository state
- [ ] export or backup the current database schema and data
- [ ] preserve `composer.lock` and current migration files as-is

### 2. Validate current state
- [ ] verify that the current app works on the current supported PHP version for Laravel 5
- [ ] confirm current package versions in `composer.lock`
- [ ] note any custom modules or third-party package paths

### 3. Prepare code for migration
- [ ] list all `str_random(...)` occurrences and plan replacement
- [ ] identify all legacy helpers and remove reliance on deprecated global helpers
- [ ] document auth routes and controllers for replacement
- [ ] record middleware classes and route middleware aliases
- [ ] keep business logic unchanged during preparation

### 4. Prepare dependency migration
- [ ] update `composer.json` only after the baseline is safe
- [ ] plan replacements for abandoned packages:
  - `fzaninotto/faker`
  - `phpoffice/phpexcel`
  - `guzzlehttp/guzzle`
  - `doctrine/dbal`
  - `barryvdh/laravel-dompdf`
  - `laravelcollective/html`
  - `yajra/laravel-datatables-oracle`
  - `phpunit/phpunit`

### 5. Plan incremental upgrade strategy
- [ ] consider upgrading through intermediate Laravel versions rather than directly to 11
- [ ] if possible, create a fresh Laravel 11 app and port modules one-by-one
- [ ] keep logic and module behavior intact during porting

### 6. Preserve business logic integrity
- [ ] do not modify business logic while preparing files
- [ ] document legacy module flows and dependencies
- [ ] avoid running `migrate:fresh` during preparation

### 7. Create a migration staging branch
- [ ] work in a dedicated branch for all migration preparation and upgrade changes
- [ ] use the new document as a reference for all later work

## Recommended next steps

1. **Do not change business logic yet.** Keep all logic intact while preparing migration notes and dependency changes.
2. **Fix the environment mismatches first.** Reconcile `composer.json` PHP target with the actual PHP/runtime to be used for migration.
3. **Replace deprecated helpers before upgrading Laravel.** This makes later framework upgrades easier.
4. **Set up a fresh Laravel 11 skeleton** if you choose the porting approach.
5. **Migrate incrementally** and validate each step with tests or smoke checks.

## Notes

This document is intended to be a pre-migration preparation file only.
It does not modify any application logic or execute database resets.
For actual migration, use this checklist together with a dedicated migration branch and backups.