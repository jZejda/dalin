<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\SportEventExportsType;
use App\Enums\SportEventType;
use App\Filament\Resources\SportEventExportResource\Pages;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SportEventExportResource extends Resource
{
    protected static ?string $model = SportEventExport::class;

    public static ?int $navigationSort = 12;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Akce/Závody';
    protected static ?string $label = 'Výstup pro pořádání';
    protected static ?string $pluralLabel = 'Výstupy pro pořádání';

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

                            TextInput::make('title')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')
                                ->required(),

                            Grid::make()->schema([
                                TextInput::make('result_path')
                                    ->required(),
                            ])->columns(1)
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),


                    // Right Column
                    Card::make()
                        ->schema([
                            Select::make('export_type')
                                ->label('Typ exportu')
                                ->options(SportEventExportsType::class)
                                ->default(SportEventExportsType::EventEntryListCat)
                                ->searchable(),

                            Select::make('file_type')
                                ->label('Typ souboru')
                                ->options([
                                    SportEventExport::FILE_XML_IOF_V3 => 'XML IOF v3',
                                ])
                                ->default(SportEventExport::FILE_XML_IOF_V3)
                                ->disablePlaceholderSelection(),

                            Select::make('sport_event_id')
                                ->label('ID závodu')
                                ->options(
                                    SportEvent::all()
                                    ->whereIn('event_type', [SportEventType::Race, SportEventType::Training, SportEventType::TrainingCamp])
                                    ->sortBy('date')
                                    ->pluck('sportEventOrisTitle', 'id')
                                ),
                            DateTimePicker::make('start_time')
                                ->label('Čas 00')
                                ->nullable(),
                            TextInput::make('sport_event_leg_id')
                                ->label('ID Etapy závodu')
                                ->nullable()

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
                TextColumn::make('title')->searchable()->limit(20),
                TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->label('Cesta')
                    ->prefix(function (SportEventExport $record): string {
                        if ($record->export_type === SportEventExportsType::EventEntryListCat) {
                            return '/startovka/';
                        } else {
                            return '/vysledky/';
                        }
                    }),
                TextColumn::make('export_type')
                    ->badge(),
                TextColumn::make('result_path')
                    ->label('Cesta k souboru'),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime('d. m. Y - H:i'),
            ])
            ->defaultPaginationPageOption(25)
            ->filters([
//                SelectFilter::make('user_id')->relationship('user_id', 'name'),
                SelectFilter::make('file_type')
                    ->options([
                        SportEventExport::FILE_XML_IOF_V3 => 'XML IOF v3',
                    ])->multiple(),
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
            'index' => Pages\ListSportEventExports::route('/'),
            'create' => Pages\CreateSportEventExport::route('/create'),
            'edit' => Pages\EditSportEventExport::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title . ' | ' . $record->updated_at->format('m. Y');
    }
}
