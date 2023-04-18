<?php

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\UserEntry;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;

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
                ExportAction::make('exportToFile')
                    ->label('Export přihlášek')
                    ->exports([
                        ExcelExport::make()
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
                Action::make('exportCsos')
                    ->label('Přihlášky ČSOS')
                    ->action(function (Collection $records) {
                        dd($records);
                    })
                    ->form([
                        Forms\Components\Textarea::make('prihlasky')
                            ->label('Prihlasky')
                            ->default(function (UserEntry $record): void {
                                dd($record);
                            }),
                    ])
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
