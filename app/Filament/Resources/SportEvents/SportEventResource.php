<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents;

use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\Action;
use App\Filament\Resources\SportEvents\Pages\ListSportEvents;
use App\Filament\Resources\SportEvents\Pages\CreateSportEvent;
use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\Pages\ViewSportEvent;
use App\Filament\Resources\SportEvents\Pages\EntrySportEvent;
use App\Enums\AppRoles;
use App\Enums\SportEventTransportType;
use App\Enums\SportEventType;
use App\Filament\Resources\SportEvents\RelationManagers\SportClassesRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\SportEventLinkRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\SportEventNewsRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\SportMarkersRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\SportServicesRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\UserCreditRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\UserEntryRelationManager;
use App\Filament\Resources\SportEvents\RelationManagers\RelayTeamsRelationManager;
use App\Models\AppSetting;
use App\Models\Club;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportLevel;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class SportEventResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SportEvent::class;

    protected static ?int $navigationSort = 10;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.events');
    }

    public static function getNavigationLabel(): string
    {
        return __('sport-event.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('sport-event.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sport-event.plural_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Model $record): string => route('filament.admin.resources.sport-events.entry', ['record' => $record]), )
            ->columns([
                ViewColumn::make('entry_type')
                    ->label(__('sport-event.table.entry_type'))
                    ->view('filament.tables.columns.entryType')
                    ->alignment(Alignment::Center)
                    ->visibleFrom('md'),
                ViewColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->tooltip(
                        fn (
                            SportEvent $record
                        ): string => $record->last_update ? __('sport-event.table.last_update_tooltip', [
                            'date' => $record->last_update->format(AppHelper::DATE_TIME_FORMAT),
                        ]) : ''
                    )
                    ->label(__('sport-event.table.name'))
                    ->view('filament.tables.columns.entry-name'),

                TextColumn::make('date')
                    ->icon('heroicon-o-calendar')
                    ->label(__('sport-event.table.date'))
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->sortable()
                    ->searchable()
                    ->description(function (SportEvent $record) {
                        $dateEnd = $record->date_end;
                        if ($dateEnd !== null) {
                            return $record->date->format('d').'-'.$record->date_end->format(AppHelper::DATE_FORMAT);
                        }

                        return '';
                    }),

                ViewColumn::make('entry_weather')
                    ->label(__('sport-event.table.forecast'))
                    ->view('filament.tables.columns.entry-forecast'),

                ViewColumn::make('user_entry')
                    ->label(__('sport-event.table.entries_count_short'))
                    ->view('filament.tables.columns.entry-user-counts'),

//                TextColumn::make('place')
//                    ->searchable()
//                    ->sortable()
//                    ->color('gray')
//                    ->label('Místo')
//                    ->limit(25, '...')
//                    ->alignLeft(),

                ViewColumn::make('entries')
                    ->label(__('sport-event.table.dates'))
                    ->view('filament.tables.columns.entryDates'),

                TextColumn::make('organization')
                    ->label(__('sport-event.table.clubs'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('region')->label(__('sport-event.table.region')),

                TextColumn::make('oris_id')
                    ->badge()
                    ->label(__('sport-event.table.oris_id'))
                    ->tooltip(
                        fn (SportEvent $record): string => EmptyType::intNotEmpty(
                            $record->oris_id
                        ) && $record->use_oris_for_entries
                            ? __('sport-event.table.oris_id_tooltip_enabled')
                            : __('sport-event.table.oris_id_tooltip_disabled')
                    )
                    ->color(
                        fn (SportEvent $record): string => EmptyType::intNotEmpty(
                            $record->oris_id
                        ) && $record->use_oris_for_entries ? 'success' : 'danger'
                    )
//                    ->color(static function ($state): string {
//                        if ($state == true) {
//                            return 'success';
//                        }
//                        return 'secondary';
//                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('date')
            ->persistSortInSession()
            ->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('event_type')
                    ->label(__('sport-event.event_type'))
                    ->options(SportEventType::enumArray()),
                SelectFilter::make('sport_id')
                    ->label(__('sport-event.filters.sport'))
                    ->options(SportList::all()->pluck('short_name', 'id')),
                   // ->default(1),
                Filter::make('date')
                    ->schema([
                        DatePicker::make('date')->default(now()->subDays(14)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            );
                    })->indicateUsing(function (array $data): ?string {
                        if (! $data['date']) {
                            return null;
                        }

                        return __('sport-event.filters.newer_than', [
                            'date' => Carbon::parse($data['date'])->format(AppHelper::DATE_FORMAT),
                        ]);
                    })->default(now()->subDays(7)),
                SelectFilter::make('discipline_id')
                    ->label(__('sport-event.filters.discipline'))
                    ->multiple()
                    ->options(SportDiscipline::all()->pluck('long_name', 'id')),
                SelectFilter::make('level_id')
                    ->label(__('sport-event.filters.level'))
                    ->options(SportLevel::all()->pluck('long_name', 'oris_id')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value])),
                    // TODO refactor
                    //                    Tables\Actions\Action::make('registr_entry')
                    //                        ->icon('heroicon-o-ticket')
                    //                        ->label('Přihlásit na závod.')
                    //                        ->url(fn (SportEvent $record): string => route('filament.admin.resources.sport-events.entry', $record))
                    //                        ->openUrlInNewTab(),
                    //                        ExportAction::make()
                    //                        ->exports([
                    //                            // Pass a string
                    //                            ExcelExport::make()
                    //                                ->withFilename(date('Y-m-d') . ' - export')
                    //                                ->withColumns([
                    //                                    Column::make('name')->heading('User name'),
                    //                                    Column::make('created_at')->heading('Creation date'),
                    //                                ]),
                    //                        ])
                ]),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    'sm' => 1,
                    'md' => 12
                ])
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make()->schema([
                                    TextInput::make('oris_id')
                                        ->label(__('sport-event.form.oris_id'))
                                        ->hint(__('sport-event.form.oris_id_hint'))
                                        ->hintIcon('heroicon-m-exclamation-triangle')
                                        ->suffixAction(
                                            fn ($state, Set $set, callable $get) => Action::make(
                                                'hledej-podle-oris-id'
                                            )
                                                ->icon('heroicon-o-magnifying-glass')
                                                ->action(function () use ($state, $set, $get) {
                                                    if (blank($state)) {
                                                        Notification::make()
                                                            ->title(__('sport-event.actions.search_by_oris_id.notification_title_missing'))
                                                            ->body(__('sport-event.actions.search_by_oris_id.notification_body_missing'))
                                                            ->danger()
                                                            ->seconds(8)
                                                            ->send();

                                                        return;
                                                    }

                                                    try {
                                                        //$client = (new GuzzleClient())->create();
                                                        $orisResponse = Http::get(
                                                            OrisApiService::ORIS_API_URL,
                                                            [
                                                                'format' => 'json',
                                                                'method' => 'getEvent',
                                                                'id' => $state,
                                                            ]
                                                        )
                                                            ->throw()
                                                            ->json('Data');

                                                    } catch (RequestException $e) {
                                                        Notification::make()
                                                            ->title(__('sport-event.common.oris_api_title'))
                                                            ->body(__('sport-event.common.oris_fetch_error'))
                                                            ->danger()
                                                            ->seconds(8)
                                                            ->send();

                                                        return;
                                                    }
                                                    $set('name', $orisResponse['Name'] ?? null);
                                                    $set('place', $orisResponse['Place'] ?? null);
                                                    $set('date', $orisResponse['Date'] ?? null);
                                                    $set('entry_date_1', $orisResponse['EntryDate1'] ?? null);
                                                    if ($get('dont_update_excluded') === false) {
                                                        $set('entry_date_2', $orisResponse['EntryDate2'] ?? null);
                                                        $set('entry_date_3', $orisResponse['EntryDate3'] ?? null);
                                                        $set(
                                                            'use_oris_for_entries',
                                                            $orisResponse['UseORISForEntries'] ?? null
                                                        );
                                                    }

                                                    $region = [];
                                                    if (isset($orisResponse['Regions'])) {
                                                        foreach ($orisResponse['Regions'] as $item) {
                                                            $region[] = $item['ID'];

                                                        }
                                                    }

                                                    $organizations = [];
                                                    if (isset($orisResponse['Org1']['Abbr'])) {
                                                        $organizations[] = $orisResponse['Org1']['Abbr'];
                                                    }
                                                    if (isset($orisResponse['Org2']['Abbr'])) {
                                                        $organizations[] = $orisResponse['Org2']['Abbr'];
                                                    }

                                                    $set('region', $region);
                                                    $set('organization', $organizations);

                                                    $set('discipline_id', $orisResponse['Discipline']['ID'] ?? null);
                                                    $set('sport_id', $orisResponse['Sport']['ID'] ?? null);
                                                    $set('level_id', $orisResponse['Level']['ID'] ?? null);

                                                    $set('start_time', $orisResponse['StartTime'] ?? null);
                                                    $set('gps_lat', $orisResponse['GPSLat'] ?? null);
                                                    $set('gps_lon', $orisResponse['GPSLon'] ?? null);

                                                    $set('entry_desc', $orisResponse['EntryDescCZ'] ?? null);

                                                    $set('event_info', $orisResponse['EventInfo'] ?? null);
                                                    $set('event_warning', $orisResponse['EventWarning'] ?? null);

                                                })
                                        ),

                                    Select::make('event_type')
                                        ->label(__('sport-event.event_type'))
                                        ->options(SportEventType::enumArray())
                                        ->default(SportEventType::Race->value),

                                    Select::make('transport_type')
                                        ->label(__('sport-event.transport_type'))
                                        ->options(SportEventTransportType::enumArray())
                                        ->default(SportEventTransportType::SelfOnly->value)
                                        ->required()
                                        ->visible(fn (): bool => AppSetting::isTransportModuleEnabled()),

                                    TextInput::make('name')
                                        ->label(__('sport-event.form.name'))
                                        ->required(),

                                ])->columns(3)->columnSpan(3),

                                Grid::make()->schema([
                                    DatePicker::make('date')
                                        ->label(__('sport-event.form.date'))
                                        ->displayFormat(AppHelper::DATE_FORMAT)
                                        ->required(),
                                    DatePicker::make('date_end')
                                        ->label(__('sport-event.form.date_end'))
                                        ->displayFormat(AppHelper::DATE_FORMAT)
                                        ->hint(__('sport-event.form.date_end_hint')),
                                    TextInput::make('stages')
                                        ->label(__('sport-event.form.stages'))
                                        ->hint(__('sport-event.form.stages_hint'))
                                        ->hintIcon('heroicon-m-exclamation-triangle')
                                        ->hintColor('warning'),
                                ])->columns(3)->columnSpan(3),

                                Grid::make()->schema([
                                    TextInput::make('alt_name')
                                        ->label(__('sport-event.form.alt_name'))
                                        ->hint(__('sport-event.form.alt_name_hint')),
                                    TextInput::make('place')
                                        ->label(__('sport-event.form.place')),

                                    TextInput::make('gps_lat')
                                        ->label(__('sport-event.form.gps_lat'))
                                        ->numeric(),

                                    TextInput::make('gps_lon')
                                        ->label(__('sport-event.form.gps_lon'))
                                        ->numeric(),
                                ])->columns(2)->columnSpan(3),

                                Grid::make()->schema([
                                    MarkdownEditor::make('entry_desc')
                                        ->label(__('sport-event.form.entry_desc')),
                                    TextInput::make('event_info')
                                        ->label(__('sport-event.form.event_info')),
                                    TextInput::make('event_warning')
                                        ->label(__('sport-event.form.event_warning')),
                                ])->columns(1)->columnSpan(3),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('sport-event.form.section_dates'))
                            ->description(__('sport-event.form.section_dates_description'))
                            ->schema([
                                TextInput::make('start_time')->label(__('sport-event.form.start_time')),
                                DateTimePicker::make('entry_date_1')->displayFormat(
                                    AppHelper::DATE_TIME_FULL_FORMAT
                                )->label(__('sport-event.form.entry_date_1')),
                                Grid::make()->schema([
                                    DateTimePicker::make('entry_date_2')->displayFormat(
                                        AppHelper::DATE_TIME_FULL_FORMAT
                                    )->label(__('sport-event.form.entry_date_2')),
                                    DateTimePicker::make('entry_date_3')->displayFormat(
                                        AppHelper::DATE_TIME_FULL_FORMAT
                                    )->label(__('sport-event.form.entry_date_3')),
                                ])->columns(2),
                            ]),

                        Section::make(__('sport-event.form.section_other'))
                            ->description(__('sport-event.form.section_other_description'))
                            ->schema([
                                Grid::make()->schema([
                                    Select::make('discipline_id')
                                        ->label(__('sport-event.form.discipline'))
                                        ->default(1)
                                        ->options(SportDiscipline::all()->pluck('long_name', 'id'))
                                        ->searchable()
                                        ->required(),
                                    Select::make('sport_id')
                                        ->label(__('sport-event.form.sport'))
                                        ->default(1)
                                        ->options(SportList::all()->pluck('short_name', 'id'))
                                        ->searchable()
                                        ->required(),

                                    Select::make('level_id')
                                        ->label(__('sport-event.form.level'))
                                        ->default(6)
                                        ->options(SportLevel::all()->pluck('long_name', 'oris_id'))
                                        ->searchable()
                                        ->required(),

                                    Select::make('organization')
                                        ->multiple()
                                        ->default([config('site-config.club.abbr')])
                                        ->options(Club::all()->pluck('name', 'abbr'))
                                        ->maxItemsMessage(__('sport-event.form.organization_max_items'))
                                        ->maxItems(2)
                                        ->searchable(),
                                ])->columns(2),
                                Select::make('region')
                                    ->multiple()
                                    ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                    ->searchable(),
                                Grid::make()->schema([
                                    Toggle::make('use_oris_for_entries')
                                        ->label(__('sport-event.form.use_oris_for_entries'))
                                        ->inline(false)
                                        ->onIcon('heroicon-s-check')
                                        ->offIcon('heroicon-m-x-mark'),

                                    Toggle::make('dont_update_excluded')
                                        ->label(__('sport-event.form.dont_update_excluded'))
                                        ->inline(false)
                                        ->onIcon('heroicon-s-check')
                                        ->offIcon('heroicon-m-x-mark')
                                        ->default(true),
                                    Toggle::make('cancelled')
                                        ->label(__('sport-event.form.cancelled'))
                                        ->inline(false)
                                        ->onIcon('heroicon-s-check')
                                        ->offIcon('heroicon-m-x-mark')
                                        ->onColor('danger')
                                        ->default(false),
                                ])->columns(3),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            UserEntryRelationManager::class,
            SportClassesRelationManager::class,
            RelayTeamsRelationManager::class,
            SportServicesRelationManager::class,
            SportMarkersRelationManager::class,
            SportEventLinkRelationManager::class,
            SportEventNewsRelationManager::class,
            UserCreditRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSportEvents::route('/'),
            'create' => CreateSportEvent::route('/create'),
            'edit' => EditSportEvent::route('/{record}/edit'),
            'view' => ViewSportEvent::route('/{record}'),
            'entry' => EntrySportEvent::route('/{record}/entry'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var SportEvent $record */
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'oris_id', 'place'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var SportEvent $record */
        return [
            __('sport-event.table.name') => $record->name,
            __('sport-event.common.place') => $record->place,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::where('cancelled', 0)->where('date', '>', Carbon::now()->startOfYear())->count();
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
//            'restore',
//            'restore_any',
//            'replicate',
//            'reorder',
            'delete',
//            'delete_any',
//            'force_delete',
//            'force_delete_any',
            'entry',
        ];
    }
}
