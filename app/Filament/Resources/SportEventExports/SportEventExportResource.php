<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventExports;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Resources\SportEventExports\Pages\ListSportEventExports;
use App\Filament\Resources\SportEventExports\Pages\CreateSportEventExport;
use App\Filament\Resources\SportEventExports\Pages\EditSportEventExport;
use App\Enums\SportEventExportsType;
use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SportEventExportResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SportEventExport::class;

    public static ?int $navigationSort = 12;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Akce/Závody';

    protected static ?string $label = 'Výstup pro pořádání';

    protected static ?string $pluralLabel = 'Výstupy pro pořádání';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Section::make()
                        ->schema([

                            TextInput::make('title')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')
                                ->required(),

                            Grid::make()->schema([
                                TextInput::make('result_path')
                                    ->required(),
                            ])->columns(1),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8,
                        ]),

                    // Right Column
                    Section::make()
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
                                ->nullable(),

                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4,
                        ]),

                ])->columnSpan(12),
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
            'index' => ListSportEventExports::route('/'),
            'create' => CreateSportEventExport::route('/create'),
            'edit' => EditSportEventExport::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title.' | '.$record->updated_at->format('m. Y');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
        ];
    }
}
