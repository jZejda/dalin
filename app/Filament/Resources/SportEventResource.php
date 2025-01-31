<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AppRoles;
use App\Enums\SportEventType;
use App\Filament\Resources\SportEventResource\Pages;
use App\Filament\Resources\SportEventResource\RelationManagers\SportClassesRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\SportEventLinkRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\SportEventNewsRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\SportMarkersRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\SportServicesRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\UserCreditRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\UserEntryRelationManager;
use App\Models\Club;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportLevel;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
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

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Akce/Závody';

    protected static ?string $navigationLabel = 'Závod';

    protected static ?string $label = 'Závod / událost';

    protected static ?string $pluralLabel = 'Závody / události';

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('entry_type')
                    ->label('Typ')
                    ->view('filament.tables.columns.entryType')
                    ->alignment(Alignment::Center)
                    ->visibleFrom('md'),

                //                TextColumn::make('name')
                //                    ->searchable()
                //                    ->label('Název')
                //                    ->sortable()
                //                    ->tooltip(fn (SportEvent $record): string => $record->last_update ? 'Poslední hromadná aktualizace: ' . $record->last_update->format('m.d.Y - H:i') : '')
                //                    ->weight('medium')
                //                    ->alignLeft()
                //                    ->limit(35)
                //                    ->color(fn (SportEvent $record): string => $record->cancelled === true ? 'danger' : '')
                //                    ->icon(fn (SportEvent $record): string => $record->cancelled === true ? 'heroicon-s-x-circle' : '')
                //                    ->iconPosition('before') // `before` or `after`
                //                    //->description(fn (SportEvent $record): string => $record->oris_id ? 'ORIS ID: ' . $record->oris_id : ''),
                //                    ->description(fn (SportEvent $record): string => $record->alt_name ? $record->alt_name : ''),

                ViewColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->tooltip(
                        fn (
                            SportEvent $record
                        ): string => $record->last_update ? 'Poslední hromadná aktualizace: '.$record->last_update->format(
                            'd.m.Y - H:i'
                        ) : ''
                    )
                    ->label('Název')
                    ->view('filament.tables.columns.entry-name'),

                TextColumn::make('date')
                    ->icon('heroicon-o-calendar')
                    ->label('Datum')
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->sortable()
                    ->searchable()
                    ->description(function (SportEvent $record) {
                        $dateEnd = $record->date_end;
                        if ($dateEnd !== null) {
                            return $record->date->format('d').' - '.$record->date_end->format('d.m.Y');
                        }

                        return '';
                    }),

                ViewColumn::make('entry_weather')
                    ->label('Předpověď')
                    ->view('filament.tables.columns.entry-forecast'),

                ViewColumn::make('user_entry')
                    ->label('Př.')
                    ->view('filament.tables.columns.entry-user-counts'),

                TextColumn::make('place')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->label('Místo')
                    ->limit(25, '...')
                    ->alignLeft(),

                ViewColumn::make('entries')
                    ->label('Terminy')
                    ->view('filament.tables.columns.entryDates'),

                TextColumn::make('organization')
                    ->label('Klub(y)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('region')->label('Region'),

                TextColumn::make('oris_id')
                    ->badge()
                    ->label('ORIS ID')
                    ->tooltip(
                        fn (SportEvent $record): string => EmptyType::intNotEmpty(
                            $record->oris_id
                        ) && $record->use_oris_for_entries
                            ? 'Přihláška do ORISu'
                            : 'Závod má přiděleno ORIS ID, prihlášení bude pouze do interního systému.'
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
                    ->sortable(),
            ])
            ->defaultSort('date')
            ->persistSortInSession()
            ->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('event_type')
                    ->label(__('sport-event.event_type'))
                    ->options(SportEventType::enumArray()),
                SelectFilter::make('sport_id')
                    ->label('Sport')
                    ->options(SportList::all()->pluck('short_name', 'id'))
                    ->default(1),
                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')->default(now()->subDays(7)),
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

                        return 'Závody novější: '.Carbon::parse($data['date'])->format('d.m.Y');
                    })->default(now()->subDays(7)),
                SelectFilter::make('discipline_id')
                    ->label('Disciplína')
                    ->multiple()
                    ->options(SportDiscipline::all()->pluck('long_name', 'id')),
                SelectFilter::make('level_id')
                    ->label('level')
                    ->options(SportLevel::all()->pluck('long_name', 'oris_id')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
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
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                Grid::make()->schema([
                                    TextInput::make('oris_id')
                                        ->label('ORIS ID')
                                        ->hint('unikátní ID závodu na ORIS stránkách')
                                        ->hintIcon('heroicon-m-exclamation-triangle')
                                        ->suffixAction(
                                            fn ($state, Set $set, callable $get) => Action::make(
                                                'hledej-podle-oris-id'
                                            )
                                                ->icon('heroicon-o-magnifying-glass')
                                                ->action(function () use ($state, $set, $get) {
                                                    if (blank($state)) {
                                                        Notification::make()
                                                            ->title('Formlulářová data')
                                                            ->body('Vyplň prosím ORIS ID závodu.')
                                                            ->danger()
                                                            ->seconds(8)
                                                            ->send();

                                                        return;
                                                    }

                                                    try {
                                                        //$client = (new GuzzleClient())->create();
                                                        $orisResponse = Http::get(
                                                            'https://oris.orientacnisporty.cz/API',
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
                                                            ->title('ORIS API')
                                                            ->body('Nepodařilo se načíst data.')
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

                                    TextInput::make('name')
                                        ->label('Název závodu/akce')
                                        ->required(),

                                ])->columns(3),

                                Grid::make()->schema([
                                    DatePicker::make('date')
                                        ->label('Datum od')
                                        ->displayFormat('d.m.Y')
                                        ->required(),
                                    DatePicker::make('date_end')
                                        ->label('Datum do')
                                        ->displayFormat('d.m.Y')
                                        ->hint('Použij u vícedenních závodů'),
                                    TextInput::make('stages')
                                        ->label('Etap')
                                        ->hint('Pouze pro etapové závody')
                                        ->hintIcon('heroicon-m-exclamation-triangle')
                                        ->hintColor('warning'),
                                ])->columns(3),

                                TextInput::make('alt_name')
                                    ->label('Alternativní název závodu')
                                    ->hint('Nebude automaticky aktualizován cronem.'),
                                TextInput::make('place')
                                    ->label('Místo'),

                                TextInput::make('gps_lat')
                                    ->label('GPS Lat')
                                    ->numeric(),

                                TextInput::make('gps_lon')
                                    ->label('GPS Lon')
                                    ->numeric(),

                                Grid::make()->schema([
                                    MarkdownEditor::make('entry_desc')
                                        ->label('Popis'),
                                    TextInput::make('event_info')
                                        ->label('Info'),
                                    TextInput::make('event_warning')
                                        ->label('Upozornění'),
                                ])->columns(1),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Section::make('Termíny')
                            ->description('Možné vypnout automatickou aktualizaci na 2. a 3. termín.')
                            ->schema([
                                TextInput::make('start_time')->label('Čas startu'),
                                DateTimePicker::make('entry_date_1')->displayFormat(
                                    AppHelper::DATE_TIME_FULL_FORMAT
                                )->label('První termín'),
                                Grid::make()->schema([
                                    DateTimePicker::make('entry_date_2')->displayFormat(
                                        AppHelper::DATE_TIME_FULL_FORMAT
                                    )->label('Druhý termín'),
                                    DateTimePicker::make('entry_date_3')->displayFormat(
                                        AppHelper::DATE_TIME_FULL_FORMAT
                                    )->label('Třetí termín'),
                                ])->columns(2),
                            ]),
                        Section::make('Ostatní parametry')
                            ->schema([
                                Grid::make()->schema([

                                    Select::make('discipline_id')
                                        ->label('Disciplína')
                                        ->default(1)
                                        ->options(SportDiscipline::all()->pluck('long_name', 'id'))
                                        ->searchable()
                                        ->required(),
                                    Select::make('sport_id')
                                        ->label('Sport')
                                        ->default(1)
                                        ->options(SportList::all()->pluck('short_name', 'id'))
                                        ->searchable()
                                        ->required(),

                                    Select::make('level_id')
                                        ->label('Level')
                                        ->default(6)
                                        ->options(SportLevel::all()->pluck('long_name', 'oris_id'))
                                        ->searchable()
                                        ->required(),

                                    Select::make('organization')
                                        ->multiple()
                                        ->default([config('site-config.club.abbr')])
                                        ->options(Club::all()->pluck('name', 'abbr'))
                                        ->maxItemsMessage('Je možné definovat pouze dva kluby')
                                        ->maxItems(2)
                                        ->searchable(),

                                    Grid::make()->schema([
                                        Select::make('region')
                                            ->multiple()
                                            ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                            ->searchable(),
                                    ])->columns(1),

                                    Grid::make()->schema([
                                        Toggle::make('use_oris_for_entries')
                                            ->label('Používá ORIS?')
                                            ->inline(false)
                                            ->onIcon('heroicon-s-check')
                                            ->offIcon('heroicon-m-x-mark'),

                                        Toggle::make('dont_update_excluded')
                                            ->label('Neaktualizovat')
                                            ->inline(false)
                                            ->onIcon('heroicon-s-check')
                                            ->offIcon('heroicon-m-x-mark')
                                            ->default(true),
                                        Toggle::make('cancelled')
                                            ->label('Zrušeno')
                                            ->inline(false)
                                            ->onIcon('heroicon-s-check')
                                            ->offIcon('heroicon-m-x-mark')
                                            ->onColor('danger')
                                            ->default(false),
                                    ])->columns(3),

                                ])->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            UserEntryRelationManager::class,
            SportClassesRelationManager::class,
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
            'index' => Pages\ListSportEvents::route('/'),
            'create' => Pages\CreateSportEvent::route('/create'),
            'edit' => Pages\EditSportEvent::route('/{record}/edit'),
            'view' => Pages\ViewSportEvent::route('/{record}'),
            'entry' => Pages\EntrySportEvent::route('/{record}/entry'),
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
            'Název' => $record->name,
            'Místo' => $record->place,
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
