# CutCode Shop

A Laravel learning project built while following a CutCode course in **October–November 2022**, approximately four years before this repository review in November 2026.

The course guided the application structure and implementation. This repository preserves that learning work; it is not presented as an independently designed commercial store or as evidence of current production readiness. The 2026 work consists of a repository audit and documentation, without rewriting the original application or upgrading its dependencies.

## Project status

**Historical, unfinished course project. Local exploration only.** The original Laravel 9 code and dependency lockfiles are intentionally preserved so that its age and learning context are visible without reading the Git history.

The storefront includes a home page, catalog, product pages and authentication flows. Cart and checkout markup exists, but there is no implemented order, payment or checkout backend. Several existing flows have known defects; see the [audit and prioritized follow-up plan](docs/AUDIT.md).

**Before running:** tracked configuration contains historical GitHub OAuth and Telegram credentials, plus a Sentry DSN. Treat these as exposed; their validity was not tested. The owner must revoke/rotate credentials at the providers. Never reuse the checked-in values. This documentation-only audit does not remove them or rewrite history. OAuth also assigns a known password to social accounts; do not connect real accounts or expose this app publicly.

## Original implementation

- Blade storefront with Tailwind CSS, Sass and Vite assets.
- Products, brands and categories, with homepage selections and cached view models.
- Catalog full-text search, sorting and a filter pipeline; filter contracts and input handling need review.
- Product properties and selectable option values through Eloquent relationships.
- Registration through a form request, DTO, action and contract; registration event and welcome notification.
- Email/password login, logout and password reset; GitHub OAuth integration with security issues documented in the audit.
- Image thumbnail generation with allowed sizes and cached files.
- Telegram HTTP client, fake implementation and custom logging integration; logger configuration needs repair.
- PHPUnit tests for authentication, events, notifications, homepage data, thumbnails, prices and Telegram.

These are course implementations, including the architectural choices below. A feature being present does not mean that its behavior is fully verified.

## Historical stack

| Component | Repository version |
| --- | --- |
| PHP | Composer constraint `^8.0.2`; audit used PHP 8.1.34 |
| Laravel | `^9.19`, locked to `9.37.0` |
| Database | MySQL; migrations use a full-text index |
| Views and assets | Blade, Tailwind CSS 3, Sass, Vite 3.2.0 |
| Authentication | Laravel session auth, Socialite 5.5.5; Sanctum is installed |
| Images | Intervention Image 2.7.2 |
| Tests and formatting | PHPUnit 9.5.26, Pint 1.2.0 |
| Diagnostics | Telescope, Debugbar, Sentry |

Laravel 9 and PHP 8.0/8.1 are outside security support in November 2026. They are retained here for historical context. A future production application would require supported dependencies and remediation of the audit findings. See the official [Laravel support policy](https://laravel.com/framework/docs/10.x/releases#support-policy) and [PHP support table](https://www.php.net/supported-versions.php).

## Code map

```text
app/                  HTTP controllers, requests, routing, product models, filters
src/Domain/Auth/      User, registration DTO, action, contract and providers
src/Domain/Catalog/   Brand/category models, builders, collections, view models
src/Services/         Telegram client, contract and fake
src/Support/          Price value object/cast, traits, flash messages, logging
resources/            Blade views, styles, scripts and original HTML templates
database/             Migrations, factories and demo seeder
tests/                Unit and feature tests with local image fixtures
```

This is a domain-oriented Laravel application with Eloquent models, rather than a fully isolated domain layer: domain classes depend on Laravel, and product-related models remain in `app/Models`. This mixed structure is preserved from the course.

## Local setup

These instructions describe the existing setup, not a verified clean-install recipe. The audit verified application bootstrap and route registration, but database-backed execution was blocked by an unavailable MySQL server.

Use an isolated local environment with PHP 8.1 for historical reproduction, Composer 2, MySQL 8, Node.js and npm. PHP needs the usual Laravel extensions, `pdo_mysql` and GD (for thumbnails). PHP 8.1 is unsupported; this is a reproduction environment, not a deployment recommendation. The frontend build was checked with Node.js 24.15.0 and npm 8.11.0 using already-installed dependencies; a fresh `npm ci` was not verified.

1. Clone the repository and enter its directory.
2. Copy `.env.example` to `.env`. Clear the historical `GITHUB_CLIENT_SECRET`, `GITHUB_CLIENT_ID` and `SENTRY_LARAVEL_DSN` values before starting anything. Leave external integrations disconnected. Keep `APP_ENV=local`, `LOG_CHANNEL=stack` and `MAIL_MAILER=log`; do not select the Telegram channel.
3. Set `APP_URL=http://127.0.0.1:8000`. Create a new, empty MySQL database dedicated to this project and set the `DB_*` values. Do not point this application at an existing valuable database.
4. Install the locked dependencies and prepare the application:

```sh
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate
php artisan db:seed
npm ci
npm run build
php artisan serve --host=127.0.0.1
```

Open `http://127.0.0.1:8000`. For asset development, use `npm run dev` in a separate terminal. Storage and `bootstrap/cache` must be writable. On Windows, creating the storage symlink may require Developer Mode or an elevated terminal.

Use `install`/`ci` to retain the original lockfiles; do not run dependency updates as part of setup. Modern Composer security policies may reject vulnerable locked packages: this is an unresolved setup limitation, not a reason to disable security checks globally. `shop:refresh` deletes image directories and recreates the database; it is not a normal startup command.

The filter pipeline omits its return value, although Eloquent preserves the builder in the current scope. Login/registration POST routes reference an unregistered `auth` rate limiter. The application booting successfully does not establish that those pages work end to end.

## Tests and checks

Create a **separate disposable MySQL database** for tests. Copy `.env.testing.example` to `.env.testing`, set `APP_ENV=testing`, use a fresh application key and configure its `DB_*` values for that database. `RefreshDatabase` recreates tables; never use the development or production database for tests. PHPUnit selects MySQL in `phpunit.xml`; SQLite is not a drop-in replacement for the full-text migration.

```sh
php artisan key:generate --env=testing
php artisan config:clear
php artisan test
composer validate --no-check-publish
vendor/bin/pint --test
composer audit --locked
npm run build
```

Pint is available but existing style compliance is not claimed. Some email validators perform DNS lookups, so the tests are not fully offline. External HTTP requests are blocked in the base test case unless explicitly faked; notifications and image storage are faked.

### Audit verification, 13 November 2026

- Three review passes: repository survey; implementation/tests review; independent checks of findings and final documentation.
- PHP syntax checks passed for 131 files; Composer metadata validation, registration of 15 application routes and the frontend build succeeded.
- All 33 tests were attempted: **3 passed; 30 errored during MySQL setup**. This is not a green suite or a measurement of functional coverage.
- Additional database-free checks reproduced a null filter result (with an Eloquent builder fallback), acceptance of an invalid sort column, a missing named auth limiter and a nonexistent Telegram logger factory class.
- Composer security audit reported advisories affecting 18 locked packages. See [the audit](docs/AUDIT.md) for the remaining check results and proposed work. No application fixes or dependency upgrades are included in this documentation pass.
