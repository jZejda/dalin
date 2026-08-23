# PHP Attributes (Laravel 13) — DaLin reference

Verified against `laravel/framework` **13.18.1** in `vendor/` (not just blog posts).
Rule of thumb for this project: **if an attribute exists for it, use the attribute — never the legacy property.**
Docblocks stay only for what attributes cannot express (PHPStan generics / array shapes, `@property` model hints).

## 1. Eloquent model — `Illuminate\Database\Eloquent\Attributes\*`

| Attribute | Signature | Replaces | Use in DaLin |
|---|---|---|---|
| `#[Fillable]` | `Fillable(array\|string ...$columns)` | `protected $fillable` | **Always** |
| `#[Guarded]` | `Guarded(array\|string ...$columns)` | `protected $guarded` | If needed |
| `#[Unguarded]` | `Unguarded()` | `protected $guarded = []` | Avoid (mass-assignment) |
| `#[Hidden]` | `Hidden(array\|string ...$columns)` | `protected $hidden` | **Always** (User) |
| `#[Visible]` | `Visible(array\|string ...$columns)` | `protected $visible` | If needed |
| `#[Appends]` | `Appends(array\|string ...$columns)` | `protected $appends` | **Always** |
| `#[Table]` | `Table(?string $name, ?string $key, ?string $keyType, ?bool $incrementing, ?bool $timestamps, ?string $dateFormat)` | `$table`, `$primaryKey`, `$keyType`, `$incrementing`, `$timestamps`, `$dateFormat` | When a model deviates from convention |
| `#[Connection]` | `Connection(UnitEnum\|string $name)` | `protected $connection` | Rare |
| `#[Touches]` | `Touches(array\|string ...$relations)` | `protected $touches` | **Always** |
| `#[DateFormat]` | `DateFormat(string $format)` | `protected $dateFormat` | Rare |
| `#[WithoutTimestamps]` | `WithoutTimestamps()` | `public $timestamps = false` | **Always** (pivot-like models) |
| `#[WithoutIncrementing]` | `WithoutIncrementing()` | `public $incrementing = false` | **Always** |
| `#[Scope]` | `Scope()` on a method | `public function scopeFoo()` naming convention | **Always** — method named `foo()`, called as `Model::foo()` |
| `#[ObservedBy]` | `ObservedBy(string\|array $classes)` | `Model::observe()` in a provider | **Always** |
| `#[ScopedBy]` | `ScopedBy(string\|array $classes)` | `static::addGlobalScope()` in `booted()` | **Always** |
| `#[UsePolicy]` | `UsePolicy(string $class)` | `Gate::policy()` / naming convention | Only when the policy breaks convention (Shield discovers by convention) |
| `#[UseFactory]` | `UseFactory(string $class)` | factory naming convention | Only when the factory breaks convention |
| `#[CollectedBy]` | `CollectedBy(string $class)` | `protected $collection` / `newCollection()` | If needed |
| `#[UseEloquentBuilder]` | `UseEloquentBuilder(string $class)` | `newEloquentBuilder()` | If needed |
| `#[UseResource]` / `#[UseResourceCollection]` | `(string $class)` | API-resource naming convention | If needed (API v1) |
| `#[Boot]` | `Boot()` on a static method | `protected static function boot()` | **Always** when boot logic exists |
| `#[Initialize]` | `Initialize()` on a method | `initializeTrait()` convention | If needed |

**No attribute exists for** — keep as-is: `casts()` method, relationship methods, accessors/mutators
(`Attribute::make()`), `$with`, `$perPage`, `$dispatchesEvents`, traits (`HasFactory`, `SoftDeletes`, …).

### Canonical model shape

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\UserCreditObserver;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'amount', 'note'])]
#[Appends(['formatted_amount'])]
#[ObservedBy(UserCreditObserver::class)]
class UserCredit extends Model
{
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', true);
    }
}
```

`#[Scope]` renames the call site: `scopeActive()` → `active()`, still used as `UserCredit::active()`.
When converting a scope, **grep for the old call site** — the query-builder call name does not change,
but the method must be renamed from `scopeActive` to `active`.

## 2. Queue jobs — `Illuminate\Queue\Attributes\*`

`#[Tries(int)]`, `#[Timeout(int)]`, `#[Backoff(int|array ...)]`, `#[MaxExceptions(int)]`,
`#[Queue(UnitEnum|string)]`, `#[Connection(UnitEnum|string)]`, `#[UniqueFor(int)]`,
`#[FailOnTimeout]`, `#[Delay(int)]`, `#[DebounceFor(int $seconds, ?int $maxWait)]`,
`#[DeleteWhenMissingModels]`, `#[WithoutRelations]` — all class-level, all replace the matching `public $property`.

## 3. Console commands — `Illuminate\Console\Attributes\*`

`#[Signature(string $signature, ?array $aliases)]`, `#[Description(string)]`, `#[Help(string)]`,
`#[Hidden]`, `#[Aliases(array)]`, `#[Usage(string)]` (repeatable).
Replaces `$signature`, `$description`, `$help`, `$hidden`.

## 4. Form requests — `Illuminate\Foundation\Http\Attributes\*`

`#[ErrorBag(string)]`, `#[RedirectTo(string)]`, `#[RedirectToRoute(string)]`,
`#[StopOnFirstFailure]`, `#[FailOnUnknownFields(bool $value = true)]`.

## 5. Factories & resources

- `#[UseModel(string $class)]` (`Illuminate\Database\Eloquent\Factories\Attributes`) replaces `protected $model`.
- `#[Collects(string)]`, `#[PreserveKeys]` (`Illuminate\Http\Resources\Attributes`) replace `$collects` / `$preserveKeys`.

## 6. Controllers / routing — `Illuminate\Routing\Attributes\Controllers\*`

- `#[Middleware(Closure|string $middleware, ?array $only, ?array $except)]` — class or method level, repeatable.
- `#[Authorize(UnitEnum|string $ability, array|string|null $models, ?array $only, ?array $except)]`.

Applies to plain controllers (`app/Http/Controllers`, API v1). **Not** to Filament resources — Filament has its own
authorization pipeline (Shield policies).

## 7. Container / DI — `Illuminate\Container\Attributes\*`

Parameter level: `#[Config('key', $default)]`, `#[CurrentUser]`, `#[Authenticated]`, `#[Auth('guard')]`,
`#[Cache('store')]`, `#[DB('conn')]`, `#[Database('conn')]`, `#[Log('channel')]`, `#[Storage('disk')]`,
`#[Tag('tag')]`, `#[RouteParameter('name')]`, `#[Give(Impl::class)]`, `#[Context('key')]`.

Class level: `#[Bind(Contract::class)]`, `#[Singleton(Contract::class)]`, `#[Scoped(Contract::class)]` — replace
manual `bind()`/`singleton()` in `AppServiceProvider`. Good fit for `app/Services/Bank/Connector/*`
implementations of `ConnectorInterface`.

## 8. Testing — `Illuminate\Foundation\Testing\Attributes\*`

`#[Seed]`, `#[Seeder(string)]`, `#[SetUp]`, `#[TearDown]`, `#[UnitTest]`.
These target `TestCase` classes/methods; Pest's functional tests mostly use `beforeEach()` / `seed()` instead —
use them only in class-based tests.

## 9. Gotchas

- **Name collisions** — import carefully or alias:
  `Eloquent\Attributes\Connection` vs `Queue\Attributes\Connection`;
  `Eloquent\Attributes\Hidden` vs `Console\Attributes\Hidden`;
  `Eloquent\Attributes\Scope` (model) vs `Container\Attributes\Scoped` (container).
- The attribute must sit **directly above `class`**, after the `@property` docblock.
- A legacy property **wins over** the attribute in some cases and silently duplicates intent in all of them —
  never leave both; delete the property in the same edit.
- Keep the `@property` docblocks — attributes do not replace them and PHPStan level 8 relies on them.
- After converting a model always run: `make phpstan` and the model's tests (`--filter`).

## 10. Rollout plan (incremental)

**Phase 0 — rules (done)**: this document + the `CLAUDE.md` convention; `SportDiscipline` is the pilot model.

**Phase 1 — new code only (immediately, zero risk)**: every new model / job / command / factory is written
attribute-first. No touching existing files.

**Phase 2 — touch-it-fix-it (ongoing)**: whenever a feature edits a model, convert that model's properties in the
same branch, but as a **separate commit** (`refactor(attrs): SportEvent`) so the feature diff stays readable.

**Phase 3 — batch conversion (planned, ~35 models)**: one PR per domain batch, each fully green before the next:
1. `SportDiscipline`, `SportClass`, `SportClassDefinition`, `SportLevel`, `SportList`, `SportRegion`
2. `SportEvent`, `SportEventExport`, `SportEventLink`, `SportEventMarker`, `SportEventNews`
3. `UserEntry`, `RelayTeam`, `RelayTeamMember`, `UserRaceProfile`
4. `User`, `UserCredit`, `UserCreditNote`, `UserParam`, `UserSetting`, `Club`
5. `BankAccount`, `BankTransaction`, `SportService`, `SportServiceOrder`, `SportServicePaymentDate`
6. `MarketOffer`, `MarketOrder`, `MarketProduct`, `TransportOffer`, `TransportRequest`, `Vehicle`
7. `Page`, `Post`, `ContentCategory`, `AppSetting`, `MailLog`

Per-batch checklist: convert `$fillable`/`$hidden`/`$appends`/`$table`-family → attributes ·
`scopeXxx()` → `#[Scope]` + rename · move `UserCredit::observe()` out of `AppServiceProvider` into `#[ObservedBy]` ·
`vendor/bin/sail bin pint --dirty` · `make phpstan` · `make pest`.

**Phase 4 — non-models**: 4 commands (`$signature`/`$description`), `SportEventFactory` (`$model` → `#[UseModel]`),
form requests, API controllers, bank connector bindings.

**Phase 5 — enforcement**: add an architecture test that fails on legacy style, e.g.
`tests/Feature/ModelAttributeConventionTest.php` scanning `app/Models/*.php` for
`protected $fillable|$hidden|$appends|$table|$primaryKey|$touches` and `function scope`.
Enable it only after Phase 3 is complete.
