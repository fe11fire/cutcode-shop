# Repository audit — 13 November 2026

## Scope and outcome

Reviewed the local repository at original commit `8ec7b7c` (23 November 2022). The first recorded commit is dated 14 October 2022. The remote advertised only `master` at the same original commit when checked. The original application, migrations and dependency lockfiles remain unchanged in this documentation pass, as requested for the audit stage.

Three passes were completed:

1. Inventory: architecture, dependencies, Git state, configuration, migrations, tests and feature boundaries.
2. Implementation review: HTTP entry points, registration/social auth, filters, models, integrations, database constraints and test assertions.
3. Cross-check: database-free reproductions of disputed findings, PHP syntax, route registration, Composer validation/security audit, frontend build, full test attempt and documentation review.

This is a useful historical learning sample. Its strongest portfolio value is the visible progression beyond basic controllers into domain grouping, DTOs, value objects and test doubles. It is not a working production store. Preserve that distinction instead of adding modern patterns to make it appear newly written.

## Architecture and strengths

`app/Routing` registrars are loaded by `App/Providers/RouteServiceProvider`; application routes are not defined primarily in `routes/web.php`. Controllers use form requests, Eloquent models and services. `src/Domain/Auth` groups the user model, registration action/contract and request DTO. `src/Domain/Catalog` groups brand/category models, custom builders, collections and cached view models. `src/Support` contains price handling, reusable traits, flash messages and Telegram logging; `src/Services` contains the Telegram client and fake.

Good interview material, with course attribution:

- [Registration action](../src/Domain/Auth/Actions/RegisterNewUserAction.php#L12) separates orchestration from the HTTP controller and dispatches `Registered`.
- [Price](../src/Support/ValueObjects/Price.php#L19) stores integer minor units, with a [custom Eloquent cast](../src/Support/Casts/PriceCast.php#L10). Explain formatting versus storage, and why precision still needs validation.
- [Brand builder](../src/Domain/Catalog/QueryBuilders/BrandQueryBuilder.php#L8) names a reusable query. [View models](../src/Domain/Catalog/ViewModels/BrandViewModel.php#L13) pair cache reads with observer invalidation.
- Migration foreign keys, unique slugs, pivot relationships, factories and local image fixtures are present.
- [Base test setup](../tests/TestCase.php#L14) fakes notifications/storage and prevents unexpected outbound HTTP calls. Authentication tests check validation, redirects, events and notifications, not just status codes.
- [Login](../app/Http/Controllers/Auth/SignInController.php#L18) regenerates the session; logout invalidates it and regenerates the CSRF token. Web middleware includes CSRF protection.

The domain layer still depends on Laravel/Eloquent. Product/property/option models remain in `app/Models`, filters depend on the global request, and registration also logs the user in. Explain these as pragmatic course boundaries, not strict DDD or framework-independent business logic.

## Critical findings — security and broken flows

| ID | Evidence | Consequence and proposed action |
| --- | --- | --- |
| C1 | [`.env.example`](../.env.example#L59), [Telegram config](../config/logging.php#L125) | Historical OAuth secret and bot token are tracked; a Sentry DSN and Telegram chat identifier are also embedded. Do not test or reuse them. Revoke/rotate at providers first, then clear example values and remove credential defaults. A DSN is not equivalent to a private API secret but should not route demo telemetry to an old account. This pass does not remove values from files/history or establish whether they remain valid. |
| C2 | [Social callback](../app/Http/Controllers/Auth/SocialAuthController.php#L30) | Every GitHub callback assigns the same known password and overwrites existing user data/password. Social accounts may therefore be accessible through password login. Use an unpredictable credential only when creating a new user, preserve existing passwords, and define explicit account-linking behavior. Test repeated callbacks, password preservation, duplicate email and absent GitHub profile fields. This was found by code review; no real provider/account was contacted. |

| C4 | [Auth routes](../app/Routing/AuthRegistrar.php#L24), [limiter setup](../app/Providers/RouteServiceProvider.php#L56) | Login/registration POST routes use `throttle:auth`, but that limiter is commented out. In this locked framework, an undefined string limiter falls through to legacy handling and throws `RuntimeException` for a guest; it does not simply provide a default rate limit. Confirmed by direct middleware invocation. Register the intended limiter and test successful POSTs and HTTP 429 after the limit. |
| C5 | `composer.lock`; `composer audit --locked` | Security advisories affect 18 locked packages, including Laravel, Guzzle, Symfony components and PHPUnit. These include development dependencies; package advisories are not proof that every vulnerability is exploitable through this app. Preserve the historical lockfiles for this archive; a public runtime requires separate dependency remediation. |

## Important findings — correctness, reliability and testing

| ID | Evidence | Consequence and proposed action |
| --- | --- | --- |
| I0 | [Filter middleware](../src/Domain/Catalog/Filters/AbstractFilter.php#L46), [catalog chain](../app/Http/Controllers/Catalog/CatalogController.php#L24) | `__invoke` omits `return $next($query)`. Direct invocation returns null, but a second probe confirmed that Eloquent substitutes its builder for a null scope result (`Builder::callScope`), so the current chain continues and filter mutations remain. This is a pipeline contract issue, not a proven catalog outage. Return the downstream value for composability and add a catalog/filter regression test. |
| I1 | [Sorting](../app/Models/Product.php#L63), [brand filter](../app/Filters/BrandFilter.php#L20), [price filter](../app/Filters/PriceFilter.php#L20) | Sort uses substring matching rather than an exact allowlist. A probe generates `ORDER BY invalid_price_column`; this is invalid-column acceptance, not demonstrated SQL injection. Brand input can be a scalar instead of an array; price bounds can be nonnumeric or reversed. Validate query parameters, map exactly `price`, `-price`, `title`, `-title`, and define inclusive/exclusive range behavior. Test malformed and boundary inputs. |
| I2 | [Catalog pagination](../app/Http/Controllers/Catalog/CatalogController.php#L26) | `paginate(9)` does not retain the search/filter/sort query string. Preserve validated parameters, and test page-two links while filtering. |
| I3 | [Telegram factory import](../config/logging.php#L3), [actual class](../src/Support/Logging/Telegram/TelegramLoggerFactory.php#L9), [production hooks](../app/Providers/AppServiceProvider.php#L49) | Config imports `Services/Logging/...`, while the class is under `Support/Logging/...`. Class lookup was confirmed false. Production slow-request/query hooks call this channel, so this can disrupt diagnostics. Fix the import, remove credential defaults, and test logger creation with a fake client. Review query-binding logging because it may expose user data. |
| I4 | [Telegram client](../src/Services/Telegram/TelegramBotApi.php#L25) | Failed outbound requests are reported using the original exception message, which can contain the bot URL/token. Define explicit timeouts/retry policy and redact credentials. After fixing logger setup, check that reporting a Telegram failure cannot recursively trigger the same channel. Recursion is a conditional risk, not reproduced behavior with the default single-file stack. Test HTTP errors/timeouts without real requests. |
| I5 | [Social callback](../app/Http/Controllers/Auth/SocialAuthController.php#L33), [GitHub ID migration](../database/migrations/2022_10_27_193141_add_github_id_to_users.php#L13) | GitHub name/email may be missing; local user columns are not nullable. An existing local email can violate uniqueness; GitHub ID has no unique constraint. Do not automatically link users by an unverified email. Define collision/null handling and identity uniqueness with tests. |
| I6 | [Product migration rollback](../database/migrations/2022_10_20_000646_create_products_table.php#L43), [full-text rollback](../database/migrations/2022_11_17_140854_add_full_text_index_to_products.php#L18), [options rollback](../database/migrations/2022_11_23_100258_create_option_values_table.php#L30) | Many `down()` methods skip work outside `local`; several added-column migrations lack a reversal; full-text rollback is empty. Option/property rollbacks drop parent tables without their pivots. Rehearse migrate/rollback/remigrate on a disposable MySQL database, then repair ordering and reversals. Do not claim rollback safety from successful forward migrations. |
| I7 | [Price test](../tests/Unit/Support/ValueObjects/PriceTest.php#L29), [OAuth exception test](../tests/Feature/App/Http/Controllers/Auth/SocialAuthControllerTest.php#L52) | Execution stops at the first expected exception, so later scenarios in the same test never run. The second price case even uses supported RUB. Split scenarios and test an unsupported currency, zero/negative precision, zero amount and negative amount. Pure Price tests should not require `RefreshDatabase`. |
| I8 | [Reset password test](../tests/Feature/App/Http/Controllers/Auth/ResetPasswordControllerTest.php#L95) | The request submits a pre-hashed password and only checks validation/redirect. Submit plain text, verify the saved hash against it, check the event and old-token reuse. Existing assertions do not prove that the intended password is usable. |
| I9 | [Product controller](../app/Http/Controllers/ProductController.php#L19) | `session('also.ID', ID)` reads with a default instead of writing recently viewed state. If retained, write explicitly and test successive views. The controller eagerly loads options but the template also reads properties; review relationship loading as part of product-page tests. |
| I10 | [Product template](../resources/views/product/show.blade.php#L113) | Description HTML is rendered unescaped. Current seeding is not proof of an exploitable user-input path, but future content management/imports need a trusted-content contract or sanitization. Test the chosen policy before allowing externally supplied HTML. |
| I11 | [Email validators](../app/Http/Requests/SignUpFormRequest.php#L38), [test environment](../.env.testing.example#L2) | DNS validation makes tests network-dependent; example test environment says `local` and contains a shared application key. PHPUnit overrides APP_ENV to testing, but other commands may not. Use a dedicated disposable MySQL database, fresh key and explicit testing environment. No CI currently reproduces this setup. |
| I12 | [Product form](../resources/views/product/show.blade.php#L75), `resources/templates/app/cart.html`, `checkout.html` | Cart/checkout are templates, not completed backend features. There are no application routes for orders/payment/checkout in the registered route list. Keep them explicitly outside implemented scope instead of building a new store for portfolio polish. |

## Cosmetic/documentation findings

The old README contained only a few installation notes. It has been replaced with course attribution, date, actual scope, architecture, setup prerequisites, test instructions and known limitations. `.gitignore` already excludes local environment files, vendor, node_modules and build output; `.env`, `.env.testing` and PHPUnit cache are not tracked. Committed examples/configuration are the credential problem, not a tracked local `.env`.

Unused imports, commented debugging calls, empty providers and registrar spelling inconsistencies remain. They do not justify repository-wide formatting or restructuring in a historical sample. Remove them only in files touched for a functional fix. Installed Sanctum/Telescope/Sentry should not be presented as completed business features merely because they appear in Composer.

## Verification and limitations

| Check | Result |
| --- | --- |
| PHP syntax, PHP 8.1.34 | 131 application/source/route/database/test PHP files checked; zero syntax errors |
| `composer validate --no-check-publish` | Passed |
| `php artisan route:list --except-vendor` | Passed, 15 application routes; does not execute those routes |
| `npm run build` | Passed using existing node_modules; fresh `npm ci` not tested |
| PHPUnit 9.5.26, `--do-not-cache-result` | 33 tests attempted, 3 assertions, 30 errors at MySQL setup; only 3 Telegram tests passed |
| Database isolation | Explicit audit-only database name; second run used an unavailable local port for quick deterministic failure. No existing database was migrated, seeded or wiped. |
| Database-free probes | Null filter return plus successful Eloquent builder fallback, invalid sort SQL, missing auth limiter/RuntimeException, nonexistent logger factory confirmed |
| `composer audit --locked` | 18 affected packages, 34 advisory entries at review time; no dependency changes |
| Pint | Installed; style compliance not tested and no formatting applied |
| Not verified | Fresh dependency installation, browser appearance, live OAuth/Telegram/Sentry, MySQL migrations/seeding/rollback, functional catalog/product behavior and meaningful coverage percentage |

The test errors establish a missing reachable MySQL service in this environment, not 30 independent application regressions. The existing `Unit` suite is partly database integration testing. Missing coverage includes the catalog pipeline, malformed query input, auth throttling, OAuth password preservation, rollback behavior, thumbnail rejection/missing files and logger initialization. No coverage percentage is claimed.

## Minimal follow-up plan

Start with **credential revocation, C2 and C4**, rather than an architectural rewrite. Each proposed change below needs discussion before implementation; these are not completed fixes.

| Step | Small change and reason | Alternative/tradeoff and interview topic | Validation | Suggested commit |
| --- | --- | --- | --- | --- |
| 1 | Revoke exposed credentials; clear examples/defaults. | History rewriting does not revoke credentials and changes the archive; rotate first. Explain configuration versus secrets. | Provider-side revocation plus tracked-file scan without printing values. | `chore: remove exposed integration credentials` |
| 2 | Generate a random credential on first OAuth creation; preserve passwords and handle missing/colliding identity data. | Nullable passwords require schema/auth changes; random credentials are a smaller repair. Do not link by email without explicit verification. | First/repeat callback, password invariant, null profile and collision tests. | `fix: secure GitHub account creation` |
| 3 | Register the auth limiter; restore the downstream filter return for composability. | Removing throttling hides intended behavior. Explain pipeline returns, Eloquent null-scope fallback and limiter keys. | Catalog response and filter tests; login/register success and 429 tests. | `fix: register auth limiter and preserve pipeline results` |
| 4 | Validate catalog input, allowlist sorting and retain pagination state. | Avoid a new generic query framework for two filters. Explain validation and SQL identifier allowlists. | Bad/scalar input, sort mapping, price edges, page-two links. | `fix: validate catalog query parameters` |
| 5 | Correct Telegram namespace; redact errors and make failure handling bounded. | Keep optional logging disabled until reliable. Explain operational side effects and fakes. | Logger creation and failure tests with HTTP fake. | `fix: make Telegram logging safe to configure` |
| 6 | Repair misleading tests and migration reversals on disposable MySQL; add a small CI job only once reproducible. | SQLite cannot validate this MySQL full-text path. Existing Pint can check touched files; a new analyzer is not necessary yet. | Full tests plus rollback/remigrate/seed rehearsal; CI MySQL service with isolated env. | `test: make legacy checks reproducible` |

Documentation commit for this stage: `docs: document 2022 course project and three-pass audit`.

### Dependency upgrade decision

Do not upgrade dependencies in this audit. Laravel is locked to 9.37.0, PHP requires `^8.0.2`, and the frontend is Vite 3/Tailwind 3. Laravel 9 security support ended on 6 February 2024 according to the official [release policy](https://laravel.com/framework/docs/10.x/releases#support-policy); PHP 8.0 and 8.1 are absent from the [supported branches](https://www.php.net/supported-versions.php) as of this review.

For an explicitly historical portfolio sample, retaining this stack and explaining its limits is reasonable. For any public deployment, a supported runtime/framework and dependency remediation are required. A future migration must account for Socialite, Intervention Image, Telescope/Sentry, Monolog handler signatures, PHPUnit and Vite changes, not only the framework version string. Allow several working days for dependency migration and regression work, potentially longer while the baseline is broken; this is a planning estimate, not a verified quote. The immediate small fixes are likely hours to a few days after MySQL is available. A separate maintained project is a better place to demonstrate a contemporary stack than rewriting this course archive.
