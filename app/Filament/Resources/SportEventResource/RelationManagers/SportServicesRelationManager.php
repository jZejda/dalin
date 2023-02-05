<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SportServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportServices';

    protected static ?string $label = 'Služba';

    protected static ?string $title = 'Služby';

    protected static ?string $recordTitleAttribute = 'service_name_cz';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('service_name_cz')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
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
            ->actions([
                Tables\Actions\EditAction::make(),
              //  Tables\Actions\DeleteAction::make(),
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
