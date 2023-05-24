<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\SportClass;
use App\Models\SportClassDefinition;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class SportClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportClasses';

    protected static ?string $recordTitleAttribute = 'class_definition_id';

    protected static ?string $label = 'Kategorie';

    protected static ?string $title = 'Kategorie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->label('Název kategorie')
                        ->required(),
                    Select::make('class_definition_id')
                        ->label('Kategorie')
                        ->required()
                        ->options(SportClassDefinition::all()->pluck('class_definition_full_label', 'id'))
                        ->searchable(),
                ])->columns(2),
                TextInput::make('distance')
                    ->label('Délka')
                    ->suffix('km')
                    ->mask(
                        fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator('.')
                        ->mapToDecimalSeparator([','])
                        ->minValue(0)
                        ->normalizeZeros()
                        ->padFractionalZeros()
                        ->thousandsSeparator(' '),
                    ),
                TextInput::make('climbing')
                    ->label('Převýšení')
                    ->suffix('m')
                    ->mask(
                        fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator('.')
                        ->mapToDecimalSeparator([','])
                        ->minValue(0)
                        ->normalizeZeros()
                        ->padFractionalZeros()
                        ->thousandsSeparator(' '),
                    ),
                TextInput::make('controls')
                    ->label('Kontrol')
                    ->mask(
                        fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(0)
                        ->integer()
                        ->minValue(0)
                        ->thousandsSeparator(' ')
                    ),
                TextInput::make('fee')
                    ->label('Poplatek')
                    ->mask(
                        fn (TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->decimalSeparator('.')
                        ->mapToDecimalSeparator([','])
                        ->minValue(0)
                        ->normalizeZeros()
                        ->padFractionalZeros()
                        ->thousandsSeparator(' '),
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kategorie')
                    ->description(fn (SportClass $record): string => $record->classDefinition->class_definition_fullLabel ?? '')
                    ->searchable(),
                TextColumn::make('oris_id')->label('ORIS ID'),
                TextColumn::make('distance')->label('Vzdálenost'),
                TextColumn::make('climbing')->label('Stoupání'),
                TextColumn::make('controls')->label('Kontrol'),
                TextColumn::make('fee')->label('Cena'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
               // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
