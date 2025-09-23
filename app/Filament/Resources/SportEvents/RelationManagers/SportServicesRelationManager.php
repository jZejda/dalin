<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SportServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportServices';

    protected static ?string $label = 'Služba';

    protected static ?string $title = 'Služby';

    protected static ?string $recordTitleAttribute = 'service_name_cz';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->schema([
                    TextInput::make('service_name_cz')
                        ->label('Název služby')
                        ->required(),
                ])->columns(1),
                DateTimePicker::make('last_booking_date_time')
                    ->label('Datum poslední možné objednávky')
                    ->required(),
                TextInput::make('unit_price')
                    ->label('Cena za jednotku')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('qty_available')
                    ->label('Volných')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('qty_already_ordered')
                    ->label('Již objednáno')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_name_cz')
                    ->label('Název služby')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_booking_date_time')
                    ->icon('heroicon-o-calendar')
                    ->label('Datum poslední objednávky')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label('Cena za jednotku')
                    ->sortable(),
                TextColumn::make('qty_available')
                    ->label('Volných')
                    ->sortable(),
                TextColumn::make('qty_already_ordered')
                    ->label('Zbývá')
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
