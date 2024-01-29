<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\EntryStatus;
use App\Filament\Resources\UserEntryResource\Infolists\UserEntryOverview;
use App\Filament\Resources\UserEntryResource\Pages;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserEntryResource extends Resource
{
    protected static ?string $model = UserEntry::class;

    protected static ?int $navigationSort = 30;
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Přihlášky';
    protected static ?string $label = 'Přihláška na závody';
    protected static ?string $pluralLabel = 'Přihlášky na závody';

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()?->hasRole('super_admin')) {
            return UserEntry::query();
        } else {
            return UserEntry::query()
                ->from('user_entries')
                ->leftJoin('user_race_profiles', 'user_race_profiles.id', '=', 'user_entries.user_race_profile_id')
                ->leftJoin('sport_events', 'sport_events.id', '=', 'user_entries.sport_event_id')
                ->where('user_race_profiles.user_id', '=', Auth::user()?->id);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sportEvent.name')
                    ->description(fn (UserEntry $record): string => $record->sportEvent->alt_name ?? '')
                    ->label('Závod')
                    ->url(fn (UserEntry $record): string => route('filament.admin.resources.sport-events.entry', ['record' => $record->sport_event_id]))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('sport_events.name', $direction);
                    }),
                TextColumn::make('class_name')
                    ->label('Kategorie')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label('Registrace'),
                TextColumn::make('sportEvent.date')
                    ->label('Datum')
                    ->dateTime('d. m. Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requested_start')
                    ->label('Start v'),
                IconColumn::make('rent_si')
                    ->label('SI')
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
                    ->label('Etapy')
                    ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                    ->searchable(),
                TextColumn::make('entry_status')
                    ->label('Stav přihlášky')
                    ->badge()
                    ->searchable(),
//                BadgeColumn::make('entry_status')
//                    ->label('Stav')
//                    ->enum(EntryStatus::enumArray())
//                    ->colors([
//                        'success' => EntryStatus::Create->value,
//                        'secondary' => EntryStatus::Edit->value,
//                        'danger' => EntryStatus::Cancel->value,
//                    ])
//                    ->searchable(),
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
                    ->label('Stav přihlášky')
                    ->options(EntryStatus::enumArray())->multiple()
                    ->default([EntryStatus::Create->value, EntryStatus::Edit->value]),
            ])
            ->actions([
                Action::make('View Information')
                    ->label('více')
                    ->icon('heroicon-m-information-circle')
                    ->infolist(UserEntryOverview::getOverview())
                    ->slideOver()
                    ->modalSubmitAction(false),
//                Tables\Actions\RestoreAction::make(),
//                DeleteAction::make()
//                    ->before(function (DeleteAction $action) {
//                        if (true) {
//                            dd('fsdfsf');
//
//                            $action->halt();
//                        }
//                    })
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListUserEntries::route('/'),
            // 'create' => Pages\CreateUserEntry::route('/create'),
            // 'edit' => Pages\EditUserEntry::route('/{record}/edit'),
        ];
    }
}
