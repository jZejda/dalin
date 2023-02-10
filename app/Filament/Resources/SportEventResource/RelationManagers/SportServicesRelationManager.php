<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\SportClassDefinition;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

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
                    ->mask(fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator('.')
                        ->mapToDecimalSeparator([','])
                        ->minValue(0)
                        ->normalizeZeros()
                        ->padFractionalZeros()
                        ->thousandsSeparator(' '),
                    ),
                TextInput::make('qty_available')
                    ->label('Volných')
                    ->mask(fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(0)
                        ->integer()
                        ->minValue(0)
                        ->thousandsSeparator(' ')
                    ),
                TextInput::make('qty_already_ordered')
                    ->label('Již objednáno')
                    ->mask(fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(0)
                        ->integer()
                        ->minValue(0)
                        ->thousandsSeparator(' ')
                    ),
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
