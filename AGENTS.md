# AGENTS.md — Dalin

Orienteering club web application built with Laravel 13, PHP 8.5, Filament v5, Tailwind CSS v4, and Pest 4.

> **Important:** Always read `docs/project_overview.md` before writing code (project context, architecture decisions, module overview).

## Architecture

- **Backend:** Laravel 13 (PHP 8.5, strict types everywhere)
- **Admin panel:** Filament v5 with Livewire v4
- **Frontend:** Blade templates + Alpine.js 3, Tailwind CSS v4, Vite 7
- **Testing:** Pest 4 (with Laravel, Livewire, Faker, Browser plugins)
- **Static analysis:** Larastan (PHPStan level 8)
- **Code style:** Laravel Pint (PSR-12 preset)
- **Infrastructure:** Docker via Laravel Sail (MySQL 8.0)

### Directory Structure

```text
app/
├── Console/          # Artisan commands
├── Enums/            # Backed string enums (HasColor, HasLabel contracts)
├── Exceptions/       # Custom exception classes
├── Exports/          # Data export logic
├── Filament/         # Admin panel (Resources, Pages, Widgets, Actions)
├── Http/Controllers/ # Auth/, Api/V1/, Cron/, Discord/, Frontend/, Ical/
├── Http/Requests/    # Form Request validation
├── Jobs/             # Queued jobs
├── Livewire/         # Livewire components
├── Mail/             # Mailable classes
├── Models/           # Eloquent models (~25)
├── Observers/        # Model observers
├── Policies/         # Authorization policies
├── Providers/        # Service providers
├── Services/         # Business logic (~44 services)
├── Shared/           # Cross-cutting utilities
└── View/             # View composers/components
tests/
├── Datasets/         # Shared test datasets
├── Feature/          # Feature tests (Filament-focused)
├── Frontend/         # Frontend integration tests
└── Unit/             # Unit tests
```

## Build & Run Commands

All commands assume Laravel Sail (Docker). Run `make bash` to enter the container first, or prefix with `./vendor/bin/sail`.

```bash
# Docker
make up                  # Start containers (sail up -d)
make down                # Stop containers
make bash                # Shell into app container

# Code style (Laravel Pint)
make lint                # Check formatting (all files)
make lint-dirty          # Check only git-dirty files
make lint-fix            # Auto-fix formatting issues
./vendor/bin/pint        # Direct pint invocation

# Static analysis (PHPStan/Larastan, level 8)
make phpstan             # Run analysis (--memory-limit=2G)
make phpstan-baseline    # Regenerate baseline

# Tests (Pest 4)
make pest                        # Run all tests
./vendor/bin/pest                # Direct pest invocation
./vendor/bin/pest tests/Unit     # Run unit tests only
./vendor/bin/pest tests/Feature  # Run feature tests only
./vendor/bin/pest --filter="test name"       # Run single test by name
./vendor/bin/pest tests/Unit/BankAccountHelperTest.php  # Run single file
./vendor/bin/pest --filter="ClassName"       # Run tests in a class/describe block

# Frontend
npm run dev              # Vite dev server (port 5173)
npm run build            # Production build

# Database
make migrate-test-database  # Reset test database
make clear                  # Clear all caches
```

## Deployment

Deploys run through [Deployer 8](https://deployer.org/docs/8.x) (`deployer/deployer`, dev dependency).
Config lives in `deploy.php` in the project root — **gitignored**, because the repo is public and the
file holds hostnames and SSH users of the shared hostings. Full procedure (including the first deploy
onto a shared hosting): **`docs/deployment.md`**.

```bash
make deploy s=demo                    # or: vendor/bin/dep deploy demo
make deploy-tag s=demo t=v13.0.1
make deploy-rollback s=demo
vendor/bin/dep deploy stage=prod      # label selector
vendor/bin/dep deploy demo --plan     # dry run, prints the task list
```

Key points:

- One codebase, several sites (`demo`, `abm`, `abm-preview`, `abm-staging`, `pbm`, `pbm-preview`),
  each with its own database, `.env` and `config/site-config.php`.
- `.env`, `config/site-config.php` and `storage/` live in `{{deploy_path}}/shared/` and survive
  releases — a deploy never touches them. Editing `config/site-config.php` in the repo does **not**
  change what a deployed site uses.
- Code is transferred with `local_archive` (`git archive` from the local repo, uploaded as a tar),
  so only **committed** code is deployed. `--strategy=archive` switches to a server-side git mirror.
- Vite assets are built locally in Sail (no Node on the hosting) and uploaded — `make up` must be
  running before a deploy.
- Per-site secrets for `dep config:upload` go into `.deploy/<alias>/` (gitignored). Never commit them.
- The document root of each site must point at `{{deploy_path}}/current/public`.

The old `deploy.sh` (rsync of the working tree) is superseded; sites still marked TODO in `deploy.php`
have not been migrated yet.

## Code Style Guidelines

### PHP General

- **Every file** starts with `declare(strict_types=1);`
- **PSR-12** formatting enforced by Laravel Pint (braces rule disabled)
- **4-space indentation**, LF line endings, UTF-8
- Run `make lint-fix` before committing

### Imports & Namespaces

- PSR-4 autoloading: `App\` → `app/`, `Tests\` → `tests/`
- Fully qualified imports at top of file (no inline `\App\...`)
- Group order: PHP built-ins, vendor, app (Pint enforces this)

### Models

- Detailed `@property` PHPDoc blocks listing all columns with types
- Use `$fillable` arrays (not `$guarded`)
- Typed `$casts` arrays for attribute casting
- Explicit foreign keys in relationships: `$this->hasMany(Entry::class, 'sport_event_id', 'id')`
- Scope methods follow `scope` prefix convention

```php
/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $date
 */
class SportEvent extends Model
{
    protected $fillable = ['name', 'date'];
    protected $casts = ['date' => 'datetime'];
}
```

### Services

- Declare as `final class`
- Use constructor injection for dependencies
- Typed method parameters and return types (no mixed unless truly necessary)

```php
final class UserCreditService
{
    public function __construct(
        private readonly CreditRepository $repository,
    ) {}

    public function getBalance(User $user): int { /* ... */ }
}
```

### Enums

- Backed string enums (`enum Foo: string`)
- Implement Filament contracts (`HasColor`, `HasLabel`) when used in admin
- Labels use translation keys: `__('enums.status.' . $this->value)`

### Controllers

- API controllers use Scribe attributes for documentation (`#[Group]`, `#[QueryParam]`, `#[Response]`)
- Extend base `Controller` class
- Return types: `JsonResponse`, `Response`, `RedirectResponse`

### Filament Resources

- Organized by domain: `Filament/Resources/{Domain}/` with `Pages/`, `RelationManagers/`, `Widgets/`
- Custom actions in `Filament/Actions/`
- Custom form components in `Filament/Forms/Components/`

### Error Handling

- Use typed exceptions extending base Laravel exceptions
- Never use empty `catch` blocks
- API errors return structured JSON responses

### Type Safety

- PHPStan level 8 — nearly strictest
- Never suppress with `@phpstan-ignore` without baseline justification
- Prefer DTOs over arrays for structured data
- Use union types and `null` over `mixed`

## Testing Conventions

Tests use **Pest 4** syntax exclusively (no PHPUnit class-based tests).

```php
// Unit test
test('bank account number is valid', function () {
    expect(BankAccountHelper::isValid('123456/0100'))->toBeTrue();
});

// Feature test with beforeEach
beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list users', function () {
    $this->actingAs($this->user);
    // ...
});

// Datasets
test('validates input', function (string $input, bool $expected) {
    expect(validate($input))->toBe($expected);
})->with([
    ['valid-input', true],
    ['', false],
]);
```

- Feature tests: `tests/Feature/` — uses `TestCase` binding, database transactions
- Unit tests: `tests/Unit/` — no framework boot, pure logic
- Frontend tests: `tests/Frontend/` — browser/Livewire interaction tests
- **New behavior must have tests. New validators must have both success and failure tests.**
- **Never disable or remove failing tests without explicit approval.**
- **Before refactoring, verify existing tests pass first.**

## Git Conventions

- **Never commit directly to main** — always use a feature branch
- Single-line commit messages: `WHAT(SCOPE): WHY`
  - WHAT: `feat`, `fix`, `docs`, `chore`, `refactor`, `test`
  - SCOPE: optional area (e.g., `auth`, `filament`, `api`)
  - WHY: imperative mood description
  - Example: `feat(entries): add bulk status update action`
- Run `make lint-fix && make phpstan && make pest` before committing
- Do not commit generated Markdown descriptions unless explicitly asked

## Pre-Commit Checklist

1. `make lint-fix` — format code
2. `make phpstan` — static analysis passes
3. `make pest` — all tests pass
4. New code has test coverage
5. Models have `@property` PHPDoc
6. Services are `final class` with typed signatures
