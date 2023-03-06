<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class UserEntryRelationManager extends RelationManager
{
    protected static string $relationship = 'userEntry';

    protected static ?string $recordTitleAttribute = 'Přihlášky';

    protected static ?string $title = 'Přihlášky';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('UserEntryName')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sportClassDefinition.name')
                    ->label('Kategorie')
                    ->searchable(),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label('Závodní profil'),
                TextColumn::make('note')
                    ->label('Poznámka'),
                TextColumn::make('club_note')
                    ->label('Klubová poznámka'),
                TextColumn::make('requested_start')
                    ->label('Start v'),
                TextColumn::make('rent_si')
                    ->label('Půjčit čip'),
                TextColumn::make('stage_x')
                    ->label('Etapa'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
