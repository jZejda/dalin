<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SportEventResource\Pages;
use App\Http\Components\Oris\GuzzleClient;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportLevel;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Models\UserCredit;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class SportEventResource extends Resource
{
    protected static ?string $model = SportEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Správa';

    protected static ?string $label = 'Závod / událost';
    protected static ?string $pluralLabel = 'Závody / události';

    protected static ?string $navigationLabel = 'Závod / událost';

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
                                ->suffixAction(fn ($state, Closure $set) =>
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
                                            $orisResponse = Http::get('https://oris.orientacnisporty.cz/API',
                                                [
                                                    'format' => 'json',
                                                    'method' => 'getEvent',
                                                    'id' => $state,
                                                ])
                                                ->throw()
                                                ->json('Data');


//                                            dd($countryData);

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
                                    $set('region', $region);

                                    $set('discipline_id', $orisResponse['Discipline']['ID'] ?? null);
                                    $set('sport_id', $orisResponse['Sport']['ID'] ?? null);
                                    $set('level_id', $orisResponse['Level']['ID'] ?? null);

                                    $set('use_oris_for_entries', $orisResponse['UseORISForEntries'] ?? null);

                                    })
                                ),
                            TextInput::make('name')
                                ->label('Název závodu/akce')
                                ->required(),
                            TextInput::make('place')
                                ->label('Místo'),
                            DatePicker::make('date')
                                ->label('Datum od')
                                ->displayFormat('d.m.Y')
                                ->required(),

                            Grid::make()->schema([
                                DateTimePicker::make('entry_date_1')->displayFormat('d.m.Y H:i:s')->required()->label('První termín'),
                                DateTimePicker::make('entry_date_2')->displayFormat('d.m.Y H:i:s')->label('Druhý termín'),
                                DateTimePicker::make('entry_date_3')->displayFormat('d.m.Y H:i:s')->label('Třetí termín'),

                                Select::make('discipline_id')
                                    ->label('Disciplína')
                                    ->options(SportDiscipline::all()->pluck('long_name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Select::make('region')
                                    ->multiple()
                                    ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                    ->searchable(),
                                Select::make('sport_id')
                                    ->label('Sport')
                                    ->options(SportList::all()->pluck('short_name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Select::make('level_id')
                                    ->label('Level')
                                    ->options(SportLevel::all()->pluck('long_name', 'oris_id'))
                                    ->searchable(),

                                Toggle::make('use_oris_for_entries')
                                    ->extraAttributes(['class' => 'mt-4'])
                                    ->label('Použít ORIS k přihláškám?')
                                    ->onIcon('heroicon-s-check')
                                    ->offIcon('heroicon-s-x'),


                            ])->columns(3),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 12
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
                    ->weight('medium')
                    ->alignLeft()
                    ->limit(50)
                    ->description(fn (SportEvent $record): string => $record->oris_id ? 'ORIS ID: ' . $record->oris_id : ''),

                TextColumn::make('place')
                    ->searchable()
                    ->sortable()
                    ->color('secondary')
                    ->label('Místo')
                    ->limit(25, '...')
                    ->alignLeft(),

                TextColumn::make('date')
                    ->icon('heroicon-o-calendar')
                    ->label('Datum')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('region')->label('Region'),

                ViewColumn::make('entries')
                    ->label('Terminy')
                    ->view('filament.tables.columns.entryDates')

            ])
            ->filters([
                SelectFilter::make('sport_id')
                    ->label('Sport')
                    ->options(SportList::all()->pluck('short_name', 'id')),
                SelectFilter::make('discipline_id')
                    ->label('Disciplína')
                    ->multiple()
                    ->options(SportDiscipline::all()->pluck('long_name', 'id')),
                SelectFilter::make('level_id')
                    ->label('level')
                    ->options(SportLevel::all()->pluck('long_name', 'oris_id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSportEvents::route('/'),
            'create' => Pages\CreateSportEvent::route('/create'),
            'edit' => Pages\EditSportEvent::route('/{record}/edit'),
            'view' => Pages\ViewSportEvent::route('/{record}'),
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

}
