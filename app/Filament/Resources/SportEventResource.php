<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SportEventResource\RelationManagers\UserEntryRelationManager;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use App\Filament\Resources\SportEventResource\Pages;
use App\Filament\Resources\SportEventResource\RelationManagers\SportClassesRelationManager;
use App\Filament\Resources\SportEventResource\RelationManagers\SportServicesRelationManager;
use App\Models\Club;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportLevel;
use App\Models\SportList;
use App\Models\SportRegion;
use Filament\Facades\Filament;
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
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class SportEventResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SportEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Akce/Závody';
    protected static ?string $navigationLabel = 'Závod';
    protected static ?string $label = 'Závod / událost';
    protected static ?string $pluralLabel = 'Závody / události';
    protected static ?string $recordTitleAttribute = 'name';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Card::make()
                        ->schema([

                            TextInput::make('oris_id')
                                ->label('ORIS ID')
                                ->hint('unikátní ID závodu na ORIS stránkách')
                                ->hintIcon('heroicon-s-exclamation')
                                ->suffixAction(
                                    fn ($state, Closure $set) =>
                                Action::make('hledej-podle-oris-id')
                                    ->icon('heroicon-o-search')
                                    ->action(function () use ($state, $set) {
                                        if (blank($state))
                                        {
                                            Filament::notify('danger', 'Vyplň prosím ORIS ID závodu.');
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
                                            Filament::notify('danger', 'Nepodařilo se načíst data.');
                                            return;
                                        }
                                    $set('name', $orisResponse['Name'] ?? null);
                                    $set('place', $orisResponse['Place'] ?? null);
                                    $set('date', $orisResponse['Date'] ?? null);
                                    $set('entry_date_1', $orisResponse['EntryDate1'] ?? null);
                                    $set('entry_date_2', $orisResponse['EntryDate2'] ?? null);
                                    $set('entry_date_3', $orisResponse['EntryDate3'] ?? null);

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

                                    $set('use_oris_for_entries', $orisResponse['UseORISForEntries'] ?? null);

                                    $set('start_time', $orisResponse['StartTime'] ?? null);
                                    $set('gps_lat', $orisResponse['GPSLat'] ?? null);
                                    $set('gps_lon', $orisResponse['GPSLon'] ?? null);

                                    $set('entry_desc', $orisResponse['EntryDescCZ'] ?? null);

                                    $set('event_info', $orisResponse['EventInfo'] ?? null);
                                    $set('event_warning', $orisResponse['EventWarning'] ?? null);

                                    })
                                ),
                            TextInput::make('name')
                                ->label('Název závodu/akce')
                                ->required(),

                            Grid::make()->schema([
                                TextInput::make('alt_name')
                                    ->label('Alternativní název závodu')
                                    ->hint('Nebude automaticky aktualizován cronem.'),
                            ])->columns(1),

                            TextInput::make('place')
                                ->label('Místo'),
                            DatePicker::make('date')
                                ->label('Datum od')
                                ->displayFormat('d.m.Y')
                                ->required(),

                            TextInput::make('gps_lat')
                                ->label('GPS Lat'),

                            TextInput::make('gps_lon')
                                ->label('GPS Lon'),

                            Grid::make()->schema([
                                MarkdownEditor::make('entry_desc')
                                    ->label('Popis'),
                                TextInput::make('event_info')
                                    ->label('Info'),
                                TextInput::make('event_warning')
                                    ->label('Upozornění')
                            ])->columns(1),

                            Grid::make()->schema([

                                Select::make('discipline_id')
                                    ->label('Disciplína')
                                    ->options(SportDiscipline::all()->pluck('long_name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Select::make('sport_id')
                                    ->label('Sport')
                                    ->options(SportList::all()->pluck('short_name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Select::make('level_id')
                                    ->label('Level')
                                    ->options(SportLevel::all()->pluck('long_name', 'oris_id'))
                                    ->searchable()
                                    ->required(),

                                Select::make('organization')
                                    ->multiple()
                                    ->options(Club::all()->pluck('name', 'abbr'))
                                    ->maxItemsMessage('Je možné definovat pouze dva kluby')
                                    ->maxItems(2)
                                    ->searchable(),

                                Select::make('region')
                                    ->multiple()
                                    ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                    ->searchable(),

                                Toggle::make('use_oris_for_entries')
                                    ->extraAttributes(['class' => 'mt-4'])
                                    ->label('Použít ORIS k přihláškám?')
                                    ->onIcon('heroicon-s-check')
                                    ->offIcon('heroicon-s-x'),

                                Toggle::make('dont_update_excluded')
                                    ->extraAttributes(['class' => 'mt-4'])
                                    ->label('Neaktualizovat vybrané')
                                    ->onIcon('heroicon-s-check')
                                    ->offIcon('heroicon-s-x')
                                    ->default(true),


                            ])->columns(3),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),

                    // Right Column
                    Section::make('Termíny')
                        ->description('Možné vypnout automatickou aktualizaci na 2. a 3. termín.')
                        ->schema([
                            TextInput::make('start_time')->label('Čas startu'),
                            DateTimePicker::make('entry_date_1')->displayFormat(AppHelper::DATE_TIME_FULL_FORMAT)->label('První termín'),
                            DateTimePicker::make('entry_date_2')->displayFormat(AppHelper::DATE_TIME_FULL_FORMAT)->label('Druhý termín'),
                            DateTimePicker::make('entry_date_3')->displayFormat(AppHelper::DATE_TIME_FULL_FORMAT)->label('Třetí termín'),
                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4
                        ]),


                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Název')
                    ->sortable()
                    ->tooltip(fn (SportEvent $record): string => $record->last_update ? 'Poslední hromadná aktualizace: ' . $record->last_update->format('m.d.Y - H:i') : '')
                    ->weight('medium')
                    ->alignLeft()
                    ->limit(50)
                    //->description(fn (SportEvent $record): string => $record->oris_id ? 'ORIS ID: ' . $record->oris_id : ''),
                    ->description(fn (SportEvent $record): string => $record->alt_name ? $record->alt_name : ''),

                TextColumn::make('date')
                    ->icon('heroicon-o-calendar')
                    ->label('Datum')
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('user_entry_count')
                    ->label('Přihlášeno')
                    ->counts('userEntry')
                    ->color(fn (SportEvent $record): string => count($record->userEntry()->get()) > 0 ? 'success' : 'info'),

                TextColumn::make('place')
                    ->searchable()
                    ->sortable()
                    ->color('secondary')
                    ->label('Místo')
                    ->limit(25, '...')
                    ->alignLeft(),

                TextColumn::make('organization')
                    ->label('Klub(y)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('region')->label('Region'),

                ViewColumn::make('entries')
                    ->label('Terminy')
                    ->view('filament.tables.columns.entryDates'),

                BadgeColumn::make('oris_id')
                    ->label('ORIS ID')
                    ->tooltip(
                        fn (SportEvent $record): string => EmptyType::intNotEmpty($record->oris_id) && $record->use_oris_for_entries
                        ? 'Přihláška do ORISu'
                        : 'Závod má přiděleno ORIS ID, prihlášení bude pouze do interního systému.'
                    )
                    ->color(fn (SportEvent $record): string => EmptyType::intNotEmpty($record->oris_id) && $record->use_oris_for_entries ? 'success' : 'danger')
//                    ->color(static function ($state): string {
//                        if ($state == true) {
//                            return 'success';
//                        }
//                        return 'secondary';
//                    })
                    ->sortable(),
            ])->defaultSort('date')

            ->filters([
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
                        return 'Závody novější: ' . Carbon::parse($data['date'])->format('d.m.Y');
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
                        ->visible(auth()->user()->hasRole(['super_admin', 'event_master'])),
                    Tables\Actions\Action::make('registr_entry')
                        ->icon('heroicon-o-ticket')
                        ->label('Přihlásit na závod.')
                        ->url(fn (SportEvent $record): string => route('filament.resources.sport-events.entry', $record))
                        ->openUrlInNewTab()
                ])
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserEntryRelationManager::class,
            SportClassesRelationManager::class,
            SportServicesRelationManager::class,
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

    protected static function getNavigationBadge(): ?string
    {
        return (string)static::$model::where('cancelled', 0)->count();
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'entry'
        ];
    }
}
