<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SportEvent;
use App\Models\UserCredit;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class UserCreditList extends Page implements HasForms, HasTable
{
    use HasPageShield;

    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?int $navigationSort = 32;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?string $navigationLabel = 'Finance';
    protected static ?string $label = 'Finance';
    protected static ?string $pluralLabel = 'Finance';
    protected static string $view = 'filament.pages.user-credit-list';

    public int|null $userId = null;

    public function mount(): void
    {
        $this->userId = auth()->user()->id;
    }

    protected function getTableQuery(): Builder
    {
        return UserCredit::where('user_id', '=', $this->userId);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label(__('user-credit.table.created_at_title'))
                ->dateTime('d.m.Y')
                ->sortable(),
            TextColumn::make('sportEvent.name')
                ->label(__('user-credit.table.sport_event_title'))
                ->url(fn (UserCredit $record): string => route('filament.resources.user-credits.view', ['record' => $record->id]))
                ->description(fn (UserCredit $record): string => $record->sportEvent?->alt_name != null ? $record->sportEvent?->alt_name : 'záznam ID: ' . (string)$record->id)
                ->sortable()
                ->searchable(),
            TextColumn::make('userRaceProfile.reg_number')
                ->label('RegNumber')
                ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? '')
                ->sortable()
                ->searchable(),
            TextColumn::make('amount')
                ->icon(fn (UserCredit $record): string => $record->amount >= 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->color(fn (UserCredit $record): string => $record->amount >= 0 ? 'success' : 'danger')
                ->label(__('user-credit.table.amount_title')),
            ViewColumn::make('user_entry')
                ->label('Komentářů')
                ->view('filament.tables.columns.user-credit-comments-count'),
            TextColumn::make('sourceUser.name')
                ->label(__('user-credit.table.source_user_title')),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'updated_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('sport_event_id')
                ->options(
                    SportEvent::all()
                        ->where('date', '>', Carbon::now()->subYear())
                        ->pluck('sport_event_oris_title', 'id')
                )
                ->searchable()
        ];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Média',
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-bookmark';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Zatím zde není žádný záznam';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Záznam bude vložen automaticky administrátorem.';
    }

    protected function getTitle(): string
    {
        return 'Finance';
    }
}
