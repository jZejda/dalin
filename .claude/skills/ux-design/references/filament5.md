# Filament 5 — UX Component Reference

Use **only** native Filament 5 components when designing inside the admin panel. Never wrap Filament pages in custom Blade layouts.

---

## Layout Components

### Forms
```php
Forms\Components\Section::make('Název sekce')
    ->description('Volitelný popis')
    ->schema([...])
    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
    ->collapsible()        // pro dlouhé sekce
    ->collapsed()          // výchozí stav schovaný

Forms\Components\Grid::make()
    ->columns(['default' => 1, 'md' => 2])
    ->schema([...])

Forms\Components\Split::make([
    Forms\Components\Section::make([...])->grow(),
    Forms\Components\Section::make([...])->grow(false),
])

Forms\Components\Tabs::make()->tabs([
    Forms\Components\Tabs\Tab::make('Základní')->schema([...]),
    Forms\Components\Tabs\Tab::make('Pokročilé')->schema([...]),
])

Forms\Components\Wizard::make([
    Forms\Components\Wizard\Step::make('Krok 1')->schema([...]),
    Forms\Components\Wizard\Step::make('Krok 2')->schema([...]),
])
```

### Infolists (read-only detail views)
```php
Infolists\Components\Section::make('Název')->schema([...])
Infolists\Components\Grid::make()->columns(2)->schema([...])
Infolists\Components\Split::make([...])
Infolists\Components\Tabs::make()->tabs([...])
```

---

## Actions

### Page-level (top-right header)
```php
// In Resource::getHeaderActions()
Actions\CreateAction::make()
Actions\EditAction::make()
Actions\DeleteAction::make()->requiresConfirmation()
Actions\Action::make('export')->label('Exportovat')->action(fn () => ...)
```

### Table row actions
```php
Tables\Actions\EditAction::make()
Tables\Actions\DeleteAction::make()->requiresConfirmation()
Tables\Actions\Action::make('custom')->label('...')->action(fn ($record) => ...)

// Group to reduce clutter when 3+ row actions exist:
Tables\Actions\ActionGroup::make([
    Tables\Actions\EditAction::make(),
    Tables\Actions\DeleteAction::make()->requiresConfirmation(),
    Tables\Actions\Action::make('...'),
])
```

### Table header (bulk + global)
```php
Tables\Actions\BulkActionGroup::make([
    Tables\Actions\DeleteBulkAction::make(),
    Tables\Actions\BulkAction::make('...')->action(fn ($records) => ...),
])
Tables\HeaderActions\CreateAction::make()
```

### Inline form actions
```php
Forms\Components\Actions::make([
    Forms\Components\Actions\Action::make('fill')->action(fn ($set) => ...),
])
```

### Confirmation for destructive actions
```php
->requiresConfirmation()
->modalHeading('Opravdu smazat?')
->modalDescription('Tato akce je nevratná.')
->modalSubmitActionLabel('Ano, smazat')
->color('danger')
```

---

## Tables

```php
// Secondary info without extra column:
Tables\Columns\TextColumn::make('name')
    ->description(fn ($record) => $record->email)

// Hide rarely-needed columns by default:
Tables\Columns\TextColumn::make('updated_at')
    ->toggleable(isToggledHiddenByDefault: true)

// Empty state:
->emptyStateHeading('Žádné záznamy')
->emptyStateDescription('Zatím zde nic není.')
->emptyStateActions([
    Tables\Actions\CreateAction::make(),
])
```

---

## Notifications & Feedback
```php
Notifications\Notification::make()
    ->title('Uloženo')
    ->success()             // nebo ->warning() / ->danger()
    ->send();

// S tělem zprávy:
Notifications\Notification::make()
    ->title('Chyba')
    ->body('Zkuste to znovu.')
    ->danger()
    ->persistent()          // nezavírá se automaticky
    ->send();
```

---

## Navigation badges
```php
// In NavigationItem or Resource:
->badge(fn () => Model::pending()->count())
->badgeColor('warning')
```
