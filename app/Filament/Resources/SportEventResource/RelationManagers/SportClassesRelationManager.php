<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\SportClass;
use App\Models\SportClassDefinition;
use Filament\Forms;
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
                Forms\Components\TextInput::make('class_definition_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('classDefinition.name')
                    ->label('ORIS ID')
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
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
              //  Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
               // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
