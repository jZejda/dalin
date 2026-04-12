<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use App\Enums\SportEventMarkerType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SportMarkersRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventMarkers';

    protected static ?string $label = 'Body zájmu';

    protected static ?string $title = 'Body zájmu';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->columnSpanFull()->schema([
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
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function buttonCreateActionVisibility(): array
    {
        // TODO z recordu nejak vytahnout jestli je oris nebo ne a pak to skryt
        return [CreateAction::make()];
    }
}
