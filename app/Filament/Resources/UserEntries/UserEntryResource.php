<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserEntries;

use App\Shared\Helpers\AppHelper;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\UserEntries\Pages\ListUserEntries;
use App\Enums\EntryStatus;
use App\Filament\Resources\UserEntries\InfoList\UserEntryOverview;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserEntryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = UserEntry::class;

    protected static ?int $navigationSort = 30;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-flag';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.users');
    }

    public static function getNavigationLabel(): string
    {
        return __('user-entry.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('user-entry.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user-entry.plural_label');
    }

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()?->hasRole('super_admin')) {
            return UserEntry::query()->with(['sportEvent.sportClasses']);
        } else {
            $userRaceProfilesIds = (new User())->getUserRaceProfilesIds(Auth::user());
            return UserEntry::query()->with(['sportEvent.sportClasses'])->whereIn('user_race_profile_id', $userRaceProfilesIds);
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sportEvent.name')
                    ->description(fn (UserEntry $record): string => $record->sportEvent->alt_name ?? '')
                    ->label(__('user-entry.table.sport_event'))
                    ->url(fn (UserEntry $record): string => route('filament.admin.resources.sport-events.entry', ['record' => $record->sport_event_id]))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('sport_events.name', $direction === 'desc' ? 'desc' : 'asc');
                    }),
                TextColumn::make('class_name')
                    ->label(__('user-entry.table.class_name'))
                    ->description(function (UserEntry $record): string {
                        $sportClass = $record->sportEvent?->sportClasses->firstWhere('name', $record->class_name);
                        if ($sportClass === null) {
                            return '';
                        }
                        $parts = array_filter([
                            $sportClass->distance ? $sportClass->distance . 'km' : null,
                            $sportClass->climbing ? $sportClass->climbing . 'm' : null,
                            $sportClass->controls ? $sportClass->controls . 'k' : null,
                        ]);
                        return implode(' | ', $parts);
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label(__('user-entry.table.race_profile')),
                TextColumn::make('sportEvent.date')
                    ->label(__('user-entry.table.date'))
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('real_start')
                    ->label(__('user-entry.table.real_start'))
                    ->dateTime('H:i')
                    ->placeholder(__('user-entry.table.real_start_none')),
                TextColumn::make('requested_start')
                    ->label(__('user-entry.table.requested_start_note'))
                    ->limit(20)
                    ->tooltip(fn (UserEntry $record): string => $record->requested_start ?? ''),
                IconColumn::make('rent_si')
                    ->label(__('user-entry.table.rent_si'))
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-m-no-symbol',
                        1 => 'heroicon-o-check',
                        default => 'heroicon-o-exclamation-circle',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('entry_stages')
                    ->badge()
                    ->separator(',')
                    ->label(__('user-entry.table.entry_stages'))
                    ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                    ->searchable(),
                TextColumn::make('entry_status')
                    ->label(__('user-entry.table.entry_status'))
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->options(
                        SportEvent::all()
                            ->where('date', '>', Carbon::now()->subYear())
                            ->pluck('sport_event_oris_title', 'id')
                    )
                    ->searchable(),
                SelectFilter::make('entry_status')
                    ->label(__('user-entry.filters.entry_status'))
                    ->options(EntryStatus::enumArray())->multiple()
                    ->default([EntryStatus::Create->value, EntryStatus::Edit->value]),
            ])
            ->recordActions([
                Action::make('View Information')
                    ->label(__('user-entry.actions.view_information.label'))
                    ->icon('heroicon-m-information-circle')
                    ->schema(UserEntryOverview::getOverview())
                    ->slideOver()
                    ->modalSubmitAction(false),
                //Tables\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->persistSortInSession()
            ->defaultSort('sportEvent.date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserEntries::route('/'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}
