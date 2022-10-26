<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SportEventExportResource\Pages;
use App\Filament\Resources\SportEventExportResource\RelationManagers;
use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\SportEventExport;
use App\Models\User;
use Filament\Forms;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SportEventExportResource extends Resource
{
    protected static ?string $model = SportEventExport::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Pořádání';
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
                                ->afterStateUpdated(function (Closure $set, $state) {
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
                                ->options([
                                    SportEventExport::ENTRY_LIST_CATEGORY => 'Startovní listina kategorie',
                                ])
                                ->default(SportEventExport::ENTRY_LIST_CATEGORY)
                                ->searchable(),

                            Select::make('file_type')
                                ->label('Typ souboru')
                                ->options([
                                    SportEventExport::FILE_XML_IOF_V3 => 'XML IOF v3',
                                ])
                                ->default(SportEventExport::FILE_XML_IOF_V3)
                                ->disablePlaceholderSelection(),

                            TextInput::make('sport_event_id')
                                ->label('ID závodu')
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
                TextColumn::make('slug')->searchable()->label('Cesta')->prefix('/startovka/'),
                BadgeColumn::make('export_type')
                    ->enum([
                        SportEventExport::ENTRY_LIST_CATEGORY => 'Startovka kategorie',
                    ])->label('Typ exportu')
                    ->colors(['primary']),
                TextColumn::make('result_path')->label('Cesta k souboru'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime('d. m. Y - H:i'),
            ])
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
