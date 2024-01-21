<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Enums\SportEventMarkerType;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class SportMarkersRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventMarkers';

    protected static ?string $label = 'Body zájmu';

    protected static ?string $title = 'Body zájmu';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    TextInput::make('label')
                        ->label('Název bodu')
                        ->required(),
                ])->columns(1),
                TextInput::make('lat')
                    ->label('GPS Lat')
                    ->required()
                    ->numeric(),
                TextInput::make('lon')
                    ->label('GPS Lon')
                    ->required()
                    ->numeric(),
                TextInput::make('desc')
                    ->label('Popis bodu'),
                Select::make('type')
                    ->label('Typ bodu')
                    ->required()
                    ->options(SportEventMarkerType::enumArray()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Název bodu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('desc')
                    ->label('Popis bodu')
                    ->sortable(),
                TextColumn::make('lat')
                    ->label('Latitude')
                    ->sortable(),
                TextColumn::make('lon')
                    ->label('Longitude')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Typ bodu')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions(self::buttonCreateActionVisibility())
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function buttonCreateActionVisibility(): array
    {
        // TODO z recordu nejak vytahnout jestli je oris nebo ne a pak to skryt
        return [Tables\Actions\CreateAction::make(),];
    }
}
