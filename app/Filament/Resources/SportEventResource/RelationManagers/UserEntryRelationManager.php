<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class UserEntryRelationManager extends RelationManager
{
    protected static string $relationship = 'userEntry';

    protected static ?string $recordTitleAttribute = 'Přihlášky';

    protected static ?string $title = 'Přihlášky';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('UserEntryName')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        //var_dump(\Route::current());

        //dd('fdsfds');

        return $table
            ->columns([
                TextColumn::make('class_name')
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
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make('exportToFile')
                    ->label('Export přihlášek')
                    ->exports([
                        ExcelExport::make()
                            //->modifyQueryUsing(fn ($query, $ownerRecord) => $query->where('sport_event_id', '=', 16)
                            ->askForFilename(date('Y-m-d') . '_export_prihlasek')
                            ->askForWriterType()
                            ->withColumns([
                                Column::make('si')->heading('SI'),
                                Column::make('userRaceProfile.reg_number')->heading('Registrační číslo'),
                                Column::make('userRaceProfile.last_name')->heading('Příjmení'),
                                Column::make('userRaceProfile.first_name')->heading('Jméno'),
                                Column::make('class_name')->heading('Kategorie'),
                                Column::make('note')->heading('Poznámka'),
                                Column::make('club_note')->heading('Klubová poznámka'),
                                Column::make('requested_start')->heading('Požadavek na start'),
                                Column::make('rent_si')->heading('Pujčit čip')->formatStateUsing(fn ($state) => str_replace('=TRUE()', 'ANO', $state)),
                                Column::make('stage_x')->heading('Etapa'),
                            ]),
                    ]),
            ]);
    }

    protected function getModelId()
    {
        return $this->id;
    }
}
