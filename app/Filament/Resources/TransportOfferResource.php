<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AppRoles;
use App\Enums\SportEventType;
use App\Enums\TransportOfferDirection;
use App\Filament\Resources\TransportOfferResource\Pages;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;

class TransportOfferResource extends Resource
{
    protected static ?int $navigationSort = 35;
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?string $navigationLabel = 'Nabídka dopravy';
    protected static ?string $label = 'Nabídka dopravy';
    protected static ?string $pluralLabel = 'Nabídka dopravy';

    protected static ?string $model = TransportOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Author')
                    ->options(User::all()->pluck('name', 'id'))
                    ->default(Auth::id())
                    ->searchable()
                    ->required(),
                Select::make('sport_event_id')
                    ->label(__('filament/common.content_category.race_relation'))
                    ->options(
                        SportEvent::all()
                            ->whereIn('event_type', [SportEventType::Race, SportEventType::Training, SportEventType::TrainingCamp])
                            ->sortBy('date')
                            ->pluck('sportEventOrisTitle', 'id')
                    ),
                Select::make('direction')
                    ->label(__('transport-offer.credit_type'))
                    ->options(TransportOfferDirection::class)
                    ->required(),
                TextInput::make('free_seats')
                    ->label(__('transport-offer.free_seats'))
                    ->integer()
                    ->minValue(1)
                    ->required(),
                TextInput::make('distance')
                    ->label(__('transport-offer.distance'))
                    ->integer()
                    ->minValue(0),
                TextInput::make('transport_contribution')
                     ->label(__('transport-offer.transport_contribution'))
                     ->numeric()
                     ->minValue(0),
                TextInput::make('description')
                    ->label(__('transport-offer.description'))
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user_id'),
            ])
            ->columns(1)
            ->inlineLabel();
    }

    public static function getEloquentQuery(): Builder
    {
        return TransportOffer::query()->where('user_id', '=', Auth::user()?->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sportEvent.name')
                    ->label('Závod'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
//            'index' => Pages\ListTransportOffers::route('/'),
//            'create' => Pages\CreateTransportOffer::route('/create'),
//            'edit' => Pages\EditTransportOffer::route('/{record}/edit'),
        ];
    }
}
