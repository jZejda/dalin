<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SportClassDefinitionResource\Pages;
use App\Models\SportClassDefinition;
use App\Models\SportList;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class SportClassDefinitionResource extends Resource
{
    protected static ?string $model = SportClassDefinition::class;

    protected static ?int $navigationSort = 100;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Správa';
    protected static ?string $label = 'Definice kategorie';
    protected static ?string $pluralLabel = 'Definice kategorií';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([

                        TextInput::make('name')
                            ->label('Název')
                            ->required(),
                        Select::make('gender')
                            ->label('Pohlaví')
                            ->options([
                                'F' => 'Žena',
                                'M' => 'Muž',
                                'A' => 'Vše',
                            ])
                            ->required(),

                        TextInput::make('age_from')
                            ->label('Věk od:')
                            ->required(),
                        TextInput::make('age_to')
                            ->label('Věk do:')
                            ->required(),

                        Select::make('sport_id')
                            ->label('Sport')
                            ->options(SportList::all()->pluck('short_name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('oris_id')
                            ->label('ORIS ID')
                            ->disabled(true),
                    ])
                    ->columns(2)
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 12
                    ]),
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
            ->defaultPaginationPageOption(25)
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
