# Laravel 11 Dependency Upgrade Plan

This document reviews the current `composer.json` dependencies and proposes the updated package set and risk levels for Laravel 11 migration.

## Current dependency review

### `php`
- Current: `^8.2`
- Proposed: `^8.2`
- Notes: Laravel 11 supports PHP 8.2 and higher. Keep this target.
- Risk: low

### `barryvdh/laravel-dompdf`
- Current: `^0.7.1`
- Proposed: `^1.2`
- Laravel 11 support: likely yes in latest 1.x branch.
- PHP 8.2 support: yes.
- Risk: moderate (wrapper compatibility and Blade integration may need verification).

### `davejamesmiller/laravel-breadcrumbs`
- Current: `^3.0` with local path repository override.
- Proposed: `^5.0`
- Laravel 11 support: likely possible in newer package versions, but this package is a migration risk due to local path override and older codebase.
- Replacement: if compatibility fails, consider replacing with a small custom breadcrumb helper or a maintained package such as `diglactic/laravel-breadcrumbs`.
- PHP 8.2 support: package maintainer offers newer branches compatible with PHP 8+.
- Risk: high

### `doctrine/dbal`
- Current: `^2.9`
- Proposed: `^3.6`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: moderate (DBAL 3 has breaking changes, but used mainly for schema operations).

### `guzzlehttp/guzzle`
- Current: `6.*`
- Proposed: `^7.7`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: low to moderate (some code may need minor update for Guzzle 7 semantics).

### `laravel/framework`
- Current: `5.*`
- Proposed: `^11.0`
- Risk: very high (major framework upgrade requires extensive code and config changes).

### `laravelcollective/html`
- Current: `^5.3`
- Proposed: `^6.4`
- Laravel 11 support: uncertain, likely possible in later versions, but must verify.
- Replacement: if compatibility fails, migrate forms to native Blade form syntax or alternate form helper package.
- PHP 8.2 support: yes in newer releases.
- Risk: moderate to high

### `yajra/laravel-datatables-oracle`
- Current: `~6.0`
- Proposed: `^10.0`
- Laravel 11 support: yes in later Yajra releases.
- PHP 8.2 support: yes.
- Risk: moderate (migration of datatable options and helpers may be needed).

## Development dependencies

### `fakerphp/faker`
- Replacement for `fzaninotto/faker`
- Proposed: `^1.25`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: low

### `mockery/mockery`
- Current: `0.9.*`
- Proposed: `^1.6`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: low

### `phpunit/phpunit`
- Current: `~5.0`
- Proposed: `^10.0`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: moderate (test code may require updates for PHPUnit 10 syntax).

### `symfony/css-selector`
- Current: `3.1.*`
- Proposed: `^6.4`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: low

### `symfony/dom-crawler`
- Current: `3.1.*`
- Proposed: `^6.4`
- Laravel 11 support: yes.
- PHP 8.2 support: yes.
- Risk: low

## General recommendations

1. **Do not run `composer update` yet.** Use this proposal to review compatibility first.
2. **Preserve current `composer.json` and `composer.lock`.** Migration work should occur on a branch.
3. **Verify package support before updating.** Some packages may require newer package names or custom replacements.
4. **Keep the local path repository only if needed.** If `davejamesmiller/laravel-breadcrumbs` is sourced locally, decide whether to continue with the local package or switch to a packagist version.
5. **Upgrade incrementally if possible.** Laravel 5 → 6 → 7 → 8 → 9 → 10 → 11 is safer than jumping directly.

## Proposal file

A proposed `composer.laravel11.proposal.json` has been created in the project root.

- It keeps the current `composer.json` untouched.
- It updates package constraints to Laravel 11-compatible versions.
- It uses PHP `^8.2`.

## Notes

- The actual migration may require additional package changes beyond this initial proposal.
- Package version suggestions are based on expected Laravel 11 compatibility and should be confirmed against each package's changelog or packagist metadata.
