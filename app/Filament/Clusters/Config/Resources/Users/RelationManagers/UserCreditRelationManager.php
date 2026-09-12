<?php

namespace App\Filament\Clusters\Config\Resources\Users\RelationManagers;

use App\Filament\Resources\UserCredits\UserCreditResource;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\Width;
use App\Models\SportEvent;
use App\Models\UserCredit;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserCreditRelationManager extends RelationManager
{
    protected static string $relationship = 'userCredits';

    protected static ?string $recordTitleAttribute = 'amount';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('users.user_credit_relation.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('users.user_credit_relation.label');
    }

    protected static function getPluralModelLabel(): ?string
    {
        return __('users.user_credit_relation.plural_label');
    }

    /**
     * Disable lazy loading so the relation manager mounts within the initial
     * HTTP request. This lets us read query string parameters (e.g.
     * `?sport_event_id=`) from the parent page URL when applying default
     * table filters; lazy-loaded relation managers mount in a separate
     * Livewire XHR that no longer carries those query params.
     */
    protected static bool $isLazy = false;

    public array $data_list = [
        'calc_columns' => [
            'amount',
        ],
    ];

    public function form(Schema $schema): Schema
    {
        return UserCreditResource::form($schema);
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
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->description(function (UserCredit $record): string {
                        return __('users.user_credit_relation.table.record_id', ['id' => $record->id]);
                    })
                    ->sortable(),
                TextColumn::make('sportEvent.name')
                    ->label(__('user-credit.table.sport_event_title'))
                    ->description(function (UserCredit $record): string {
                        $description = '';
                        if (!is_null($record->sportEvent?->alt_name)) {
                            $description = $record->sportEvent->alt_name;
                        } else {
                            if (!is_null($record->sportEvent?->id)) {
                                $description = __('users.user_credit_relation.table.event_internal_id', ['id' => $record->sportEvent->id]);
                            }
                        }
                        return $description;
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label(__('users.user_credit_relation.table.registration'))
                    ->html()
                    ->formatStateUsing(fn ($state, UserCredit $record): HtmlString => new HtmlString(
                        (string) view('components.race-profile-identity', [
                            'profile' => $record->userRaceProfile,
                            'size' => 'sm',
                        ])
                    ))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->icon(fn (UserCredit $record): string => $record->amount >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color(fn (UserCredit $record): string => $record->amount >= 0 ? 'success' : 'danger')
                    ->label(__('user-credit.table.amount_title'))
                    ->summarize(Sum::make())->money('CZK')->label(__('users.user_credit_relation.table.amount_total')),
                ViewColumn::make('user_entry')
                    ->label(__('users.user_credit_relation.table.comments'))
                    ->view('filament.tables.columns.user-credit-comments-count'),
                ViewColumn::make('sourceUser.name')
                    ->label(__('user-credit.table.source_user_title'))
                    ->view('filament.tables.columns.user-badge'),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->label(__('users.user_credit_relation.filters.sport_event'))
                    ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id'))
                    ->default(fn (): ?int => request()->integer('sport_event_id') ?: null),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('users.user_credit_relation.filters.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('users.user_credit_relation.filters.created_until')),
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth(Width::SevenExtraLarge),
                    EditAction::make()
                        ->modalWidth(Width::SevenExtraLarge),
                ]),
            ])
            ->toolbarActions([
                ExportBulkAction::make('exportToFile')
                    ->label(__('users.user_credit_relation.actions.export.label'))
                    ->exports([
                        ExcelExport::make()
                            //->modifyQueryUsing(fn ($query, $ownerRecord) => $query->where('sport_event_id', '=', 16)
                            ->askForFilename(date('Y-m-d') . '_export_financí')
                            ->askForWriterType()
                            ->withColumns([
                                Column::make('created_at')
                                    ->heading(__('users.user_credit_relation.actions.export.col_created_at'))
                                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format(AppHelper::DATE_FORMAT)),
                                Column::make('sportEvent.name')->heading(__('users.user_credit_relation.actions.export.col_event_name')),
                                Column::make('sportEvent.alt_name')->heading(__('users.user_credit_relation.actions.export.col_event_alt_name')),
                                Column::make('userRaceProfile.reg_number')->heading(__('users.user_credit_relation.actions.export.col_reg_number')),
                                Column::make('amount')->heading(__('users.user_credit_relation.actions.export.col_amount')),
                                Column::make('sourceUser.name')->heading(__('users.user_credit_relation.actions.export.col_source_user')),
                            ]),
                    ]),
            ]);
    }
}
