<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\SportClass;
use App\Models\SportClassDefinition;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SportClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportClasses';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Kategorie';

    protected static ?string $title = 'Kategorie';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->label('Název kategorie')
                        ->required(),
                    Select::make('class_definition_id')
                        ->label('Definice kategorie (věk/gender)')
                        ->required()
                        ->options(SportClassDefinition::all()->pluck('class_definition_full_label', 'id'))
                        ->searchable(),
                ])->columns(2),
                TextInput::make('distance')
                    ->label('Délka')
                    ->suffix('km')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('climbing')
                    ->label('Převýšení')
                    ->suffix('m')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('controls')
                    ->label('Kontrol')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('fee')
                    ->label('Poplatek')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kategorie')
                    ->description(
                        fn (SportClass $record): string => $record->classDefinition->class_definition_fullLabel ?? ''
                    )
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
