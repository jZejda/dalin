<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\SportEventType;
use App\Enums\TransportOfferDirection;
use App\Filament\Resources\TransportOfferResource\Pages;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\User;
use App\Models\UserCredit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('direction')
                    ->label(__('transport-offer.direction'))
                    ->options(TransportOfferDirection::class)
                    ->required(),
                TextInput::make('free_seats')
                    ->label(__('transport-offer.free_seats'))
                    ->integer()
                    ->minValue(1)
                    ->required(),
                DatePicker::make('date')
                    ->label(__('transport-offer.date'))
                    ->required(),
                Select::make('user_id')
                    ->label(__('transport-offer.driver'))
                    ->options(
                        function (): Collection|array {
                            if (Auth::id() !== 1) {
                                return ['fsfsf', Auth::id()];
                            } else {
                                return User::all()->pluck('name', 'id');
                            }
                        }
                    )
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
                TextInput::make('distance')
                    ->label(__('transport-offer.distance'))
                    ->integer()
                    ->minValue(0)
                    ->suffix('km'),
                TextInput::make('transport_contribution')
                     ->label(__('transport-offer.transport_contribution'))
                     ->numeric()
                     ->minValue(0),
                Grid::make()->schema([
                    MarkdownEditor::make('description')
                        ->label(__('transport-offer.description'))
                ])->columns(1),

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
                TextColumn::make('user.name')
                    ->label(__('transport-offer.driver'))
                    ->badge()
                    ->color(static function ($state): string {
                        if ($state === auth()->user()?->name) {
                            return 'success';
                        }
                        return 'gray';
                    })
                    ->searchable(),
                TextColumn::make('free_seats')
                    ->label(__('transport-offer.free_seats')),
                TextColumn::make('direction')
                    ->icon(fn (TransportOffer $record): ?string => $record->direction->getIcon())
                    ->color(fn (TransportOffer $record): ?string => $record->direction->getColor())
                    ->label(__('transport-offer.direction')),
                TextColumn::make('distance')
                    ->label(__('transport-offer.distance')),
                TextColumn::make('description')
                    ->label(__('transport-offer.description'))
            ])
            ->defaultGroup('sport_event_id')
            ->groups([
                Group::make('sport_event_id')
                    ->label('Podle Akce')
                    ->getTitleFromRecordUsing(fn (TransportOffer $record): string => ucfirst($record->sportEvent->name ?? ''))
                    ->getDescriptionFromRecordUsing(fn (TransportOffer $record): string => $record->sportEvent?->alt_name ?? ''),
            ])
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->label('Závod')
                    ->options(SportEvent::all()->sortBy('date')->pluck('sport_event_last_cost_calculate', 'id')),
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')->default(now()->subDays(7)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            );
                    })->indicateUsing(function (array $data): ?string {
                        if (!$data['date']) {
                            return null;
                        }
                        return 'Nabídka dopravy od: ' . Carbon::parse($data['date'])->format('d.m.Y');
                    })->default(now()->subDays(7)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListTransportOffers::route('/'),
            // 'create' => Pages\CreateTransportOffer::route('/create'),
            // 'edit' => Pages\EditTransportOffer::route('/{record}/edit'),
        ];
    }
}
