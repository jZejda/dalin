<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserEntryResource\Pages;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Carbon\Carbon;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
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
                ->from('user_entries', 'ue')
                ->leftJoin('user_race_profiles AS urp', 'urp.id', '=', 'ue.user_race_profile_id')
                ->where('urp.user_id', '=', Auth::user()->id)
                ->orderByDesc('ue.created_at');
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
                    ->url(fn (UserEntry $record): string => route('filament.resources.sport-events.entry', ['record' => $record->sport_event_id]))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sportClassDefinition.name')
                    ->label('Kategorie')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label('Registrace'),
                TextColumn::make('sportEvent.date')
                    ->label('Konání')
                    ->dateTime('d. m. Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Poznámka'),
                TextColumn::make('club_note')
                    ->label('Klubová poznámka'),
                TextColumn::make('requested_start')
                    ->label('Start v'),
                TextColumn::make('rent_si')
                    ->label('Půjčit čip'),
                TextColumn::make('stage_x')
                    ->label('Etapa'),
            ])
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->options(
                        SportEvent::all()
                            ->where('date', '>', Carbon::now()->subYear())
                            ->pluck('sport_event_oris_title', 'id')
                    )
                    ->searchable()
            ])
            ->actions([
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
            ]);
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
