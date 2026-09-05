# Filament UI Conventions — DaLin reference

Two standing conventions for the admin panel: icons are always Lucide, and a `User`'s identity is
always rendered through one of two shared Blade components — never hand-rolled.

## 1. Icons — always Lucide, never Heroicons

Package: `codewithdennis/filament-lucide-icons`, enum: `CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon`.

```php
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;

protected static string | \BackedEnum | null $navigationIcon = LucideIcon::User;
```

- Applies everywhere an icon is set: `$navigationIcon` on resources/pages/clusters, and
  `->emptyStateIcon()` on tables. **Keep the two in sync** — a page's nav icon and its table's
  empty-state icon should be the same `LucideIcon` case, or they drift apart the next time either
  one changes (this happened in `app/Filament/Clusters/Other/Pages/MyVehicleList.php`,
  `MyMarketOfferList.php`, `MyMarketOrderList.php` — the empty-state icon still had the old
  Heroicon string after the nav icon was already switched to Lucide)
- Existing `heroicon-o-*` / `heroicon-s-*` strings predate this convention. No mass-migration
  needed — replace with the matching `LucideIcon` case whenever you're already touching a file's
  icon, same incremental approach as the [PHP Attributes](php-attributes.md) rollout
- Find the right case name in `vendor/codewithdennis/filament-lucide-icons/src/Enums/LucideIcon.php`
  (case names follow the Lucide icon-set names in PascalCase, e.g. `book-user` → `BookUser`)
- Reference: `app/Filament/Clusters/Other/OtherCluster.php` and every page inside the `Other` cluster

## 2. User identity — two shared components, never hand-rolled

Location: `resources/views/components/`. Both read a `User`'s `initials`, `badge_color` and
`avatar_url` accessors (see `App\Models\User`) — nothing about a user's identity should be
rendered by directly interpolating `$user->name` / `$user->email` outside these two components.

| Component | Renders | Use for |
|---|---|---|
| `<x-user-identity :user="$user" size="sm" />` | Round badge (initials, or the uploaded avatar) + name (bold) + email (muted), stacked two lines | Primary "whose record is this" column — Uživatel, Autor |
| `<x-user-badge :initials="..." :color="..." :avatar-url="..." size="sm" />` | Badge only, no text | Secondary/audit-trail column where space matters — Zapsal, Spustil uživatel, Nabízí |

Sizes: `xs` (24px), `sm` (32px), `md` (40px, default), `lg` (48px), `xl` (72px — the live preview on
`/admin/other/profile` uses this).

### Status indicator dot

Both components accept `:dot-color` — a `BadgeColor` case or a raw CSS color string — for a small
top-right dot with a white ring, shown over either the initials or an uploaded avatar image alike.
`null` (the default) means no dot at all.

`x-user-identity` sets this automatically: red when `$user->isActive()` is false, no dot otherwise.
When calling `x-user-badge` directly (the compact variant), the caller decides — see the `ViewColumn`
views below for the established pattern.

```blade
<x-user-badge :initials="$user->initials" :color="$user->badge_color" :dot-color="BadgeColor::Red" />
```

### Wiring into a Filament table — use the ViewColumn views, don't call the components directly

Two matching views live in `resources/views/filament/tables/columns/`:

```php
use Filament\Tables\Columns\ViewColumn;

ViewColumn::make('user.name')
    ->label(__('...'))
    ->view('filament.tables.columns.user-identity'),   // full

ViewColumn::make('sourceUser.name')
    ->label(__('...'))
    ->view('filament.tables.columns.user-badge'),      // compact
```

Both views derive the relation to load from the column's own name — `Str::beforeLast($column->getName(), '.')`
— so `user.name`, `user.userIdentification` and `sourceUser.name` all resolve correctly without any
extra configuration. When the relation is `null` (e.g. a system-generated mail log with no source
user), `user-badge` falls back to a neutral gray badge showing "N/A"; `user-identity` renders nothing.

`->searchable()` / `->sortable()` on the column keep working as normal — they operate on the
underlying dotted relation path, independent of which view renders the cell.

Reference implementation: `app/Filament/Resources/UserCredits/UserCreditResource.php` — "Uživatel"
(full) and "Zapsal" (compact) side by side in the same table.

### Security note for anything resembling an avatar upload

If you ever add another user-facing avatar/file upload bound directly to a Livewire property (no
`statePath`), read `App\Filament\Clusters\Other\Pages\UserProfileSettings::resolveAvatarPath()`
first. A `FileUpload` field with no `statePath` is a plain client-writable Livewire property — its
value must be validated against the expected directory and checked to actually exist on disk before
being trusted, exactly as that method does; Filament's own `BaseFileUpload::saveUploadedFiles()`
docblock documents the same risk.
