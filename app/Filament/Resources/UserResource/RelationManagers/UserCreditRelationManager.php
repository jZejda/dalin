<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\SportEvent;
use App\Models\UserCredit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserCreditRelationManager extends RelationManager
{
    protected static string $relationship = 'userCredits';

    protected static ?string $recordTitleAttribute = 'amount';

    protected static ?string $label = 'Finance';

    protected static ?string $title = 'Finance';

    public array $data_list = [
        'calc_columns' => [
            'amount',
        ],
    ];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    //    protected function getTableContentFooter(): ?View
    //    {
    //        return view('filament.resources.user-resource.tables.user-credit-footer', $this->data_list);
    //    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('user-credit.table.created_at_title'))
                    ->dateTime('d.m.Y')
                    ->description(function (UserCredit $record): string {
                        return 'id: '. $record->id;
                    })
                    ->sortable(),
                TextColumn::make('sportEvent.name')
                    ->label(__('user-credit.table.sport_event_title'))
                    ->url(fn (UserCredit $record): string => route('filament.admin.resources.user-credits.view', ['record' => $record->id]))
                    //->description(fn (UserCredit $record): string => $record->sportEvent?->alt_name != null ? $record->sportEvent?->alt_name : 'nepřiřazeno k závodu')
                    ->description(function (UserCredit $record): string {
                        $description = '';
                        if (!is_null($record->sportEvent?->alt_name)) {
                            $description = $record->sportEvent->alt_name;
                        } else {
                            if (!is_null($record->sportEvent?->id)) {
                                $description = 'interní id závodu: ' . $record->sportEvent->id;
                            }
                        }
                        return $description;
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label('RegNumber')
                    ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? '')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->icon(fn (UserCredit $record): string => $record->amount >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color(fn (UserCredit $record): string => $record->amount >= 0 ? 'success' : 'danger')
                    ->label(__('user-credit.table.amount_title'))
                    ->summarize(Sum::make())->money('CZK')->label('Celkem'),
                ViewColumn::make('user_entry')
                    ->label('Komentářů')
                    ->view('filament.tables.columns.user-credit-comments-count'),
                TextColumn::make('sourceUser.name')
                    ->label(__('user-credit.table.source_user_title')),
            ])
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->label('Závod')
                    ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id')),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Datum od'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Datum do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make('exportToFile')
                    ->label('Export financi uživatele')
                    ->exports([
                        ExcelExport::make()
                            //->modifyQueryUsing(fn ($query, $ownerRecord) => $query->where('sport_event_id', '=', 16)
                            ->askForFilename(date('Y-m-d') . '_export_financí')
                            ->askForWriterType()
                            ->withColumns([
                                Column::make('created_at')
                                    ->heading('Vytvořeno dne')
                                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d.m.Y')),
                                Column::make('sportEvent.name')->heading('Název události'),
                                Column::make('sportEvent.alt_name')->heading('Alternativní název'),
                                Column::make('userRaceProfile.reg_number')->heading('Registrace'),
                                Column::make('amount')->heading('Částka'),
                                Column::make('sourceUser.name')->heading('Zapsal'),
                            ]),
                    ]),
            ]);
    }
}
