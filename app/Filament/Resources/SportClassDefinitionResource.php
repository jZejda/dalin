<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SportClassDefinitionResource\Pages;
use App\Models\SportClassDefinition;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class SportClassDefinitionResource extends Resource
{
    protected static ?string $model = SportClassDefinition::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->description(fn (SportClassDefinition $record): string => $record->class_definition_fullLabel ?? ''),
                Tables\Columns\TextColumn::make('age_from')
                    ->label('Věk od')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('age_to')
                    ->label('Věk do')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sport.short_name')
                    ->label('Sport')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('oris_id')
                    ->label('ORIS ID'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSportClassDefinitions::route('/'),
            'create' => Pages\CreateSportClassDefinition::route('/create'),
            'edit' => Pages\EditSportClassDefinition::route('/{record}/edit'),
        ];
    }
}
