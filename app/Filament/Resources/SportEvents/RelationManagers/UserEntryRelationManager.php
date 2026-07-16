<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserEntryRelationManager extends RelationManager
{
    protected static string $relationship = 'userEntry';

    protected static ?string $recordTitleAttribute = 'Přihlášky';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_entries.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('UserEntryName')
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
                    ->label(__('sport-event.relation_entries.table.class'))
                    ->searchable(),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label(__('sport-event.relation_entries.table.race_profile')),
                TextColumn::make('note')
                    ->label(__('sport-event.relation_entries.table.note')),
                TextColumn::make('club_note')
                    ->label(__('sport-event.relation_entries.table.club_note')),
                TextColumn::make('requested_start')
                    ->label(__('sport-event.relation_entries.table.requested_start')),
                TextColumn::make('rent_si')
                    ->label(__('sport-event.relation_entries.table.rent_si')),
                TextColumn::make('stage_x')
                    ->label(__('sport-event.relation_entries.table.stage')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make('exportToFile')
                    ->label(__('sport-event.relation_entries.export.label'))
                    ->exports([
                        ExcelExport::make()
                            //->modifyQueryUsing(fn ($query, $ownerRecord) => $query->where('sport_event_id', '=', 16)
                            ->askForFilename(date('Y-m-d').'_export_prihlasek')
                            ->askForWriterType()
                            ->withColumns([
                                Column::make('si')->heading(__('sport-event.relation_entries.export.si')),
                                Column::make('userRaceProfile.reg_number')->heading(__('sport-event.relation_entries.export.reg_number')),
                                Column::make('userRaceProfile.last_name')->heading(__('sport-event.relation_entries.export.last_name')),
                                Column::make('userRaceProfile.first_name')->heading(__('sport-event.relation_entries.export.first_name')),
                                Column::make('class_name')->heading(__('sport-event.relation_entries.export.class')),
                                Column::make('note')->heading(__('sport-event.relation_entries.export.note')),
                                Column::make('club_note')->heading(__('sport-event.relation_entries.export.club_note')),
                                Column::make('requested_start')->heading(__('sport-event.relation_entries.export.requested_start')),
                                Column::make('rent_si')->heading(__('sport-event.relation_entries.export.rent_si'))->formatStateUsing(
                                    fn ($state) => str_replace('=TRUE()', __('sport-event.relation_entries.export.rent_si_yes'), $state)
                                ),
                                Column::make('stage_x')->heading(__('sport-event.relation_entries.export.stage')),
                            ]),
                    ]),
            ]);
    }
}
