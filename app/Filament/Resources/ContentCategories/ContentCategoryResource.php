<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentCategories;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\TextSize;
use App\Filament\Resources\ContentCategories\RelationManagers\PageRelationManager;
use App\Filament\Resources\ContentCategories\Pages\ListContentCategories;
use App\Filament\Resources\ContentCategories\Pages\CreateContentCategory;
use App\Filament\Resources\ContentCategories\Pages\EditContentCategory;
use App\Enums\SportEventType;
use App\Models\ContentCategory;
use App\Models\SportEvent;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentCategoryResource extends Resource implements HasShieldPermissions
{
    protected static ?int $navigationSort = 69;
    protected static ?string $model = ContentCategory::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Obsah';
    protected static ?string $label = 'Kategorie';
    protected static ?string $pluralLabel = 'Kategorie';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    Section::make(__('filament/common.content_category.create_heading'))
                        ->description(__('filament/common.content_category.create_description'))
                        ->schema([
                            TextInput::make('title')
                                ->label(__('filament/common.content_category.title'))
                                ->reactive()
                                ->afterStateUpdated(function (Set $set, $state, $context) {
                                    if ($context === 'edit') {
                                        return;
                                    }

                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')
                                ->label(__('filament/common.slug'))
                                ->required()
                                ->maxLength(255)
                                ->rules(['alpha_dash'])
                                ->unique(ignoreRecord: true),
                            Select::make('sport_event_id')
                                ->label(__('filament/common.content_category.race_relation'))
                                ->options(
                                    SportEvent::all()
                                    ->whereIn('event_type', [SportEventType::Race, SportEventType::Training, SportEventType::TrainingCamp])
                                    ->sortBy('date')
                                    ->pluck('sportEventOrisTitle', 'id')
                                ),
                            Grid::make()
                                ->schema([
                                    TextInput::make('description')
                                    ->label(__('filament/common.content_category.description')),
                                ])
                                ->columns(1)
                                ->columnSpan(3),
                        ])
                        ->columns(3)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 12,
                        ]),

                ])->columnSpan(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament/common.content_category.title'))
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Medium),
                TextColumn::make('description')
                    ->label(__('filament/common.content_category.description'))
                    ->limit(50),
                TextColumn::make('slug')
                    ->label(__('filament/common.slug')),
            ])
            ->defaultPaginationPageOption(25)
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentCategories::route('/'),
            'create' => CreateContentCategory::route('/create'),
            'edit' => EditContentCategory::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var ContentCategory $record */
        return [
            'Slug' => $record->slug ?? '---',
            'Počet příspěvků' => (string)$record->page()->count(),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var ContentCategory $record */
        return $record->title;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}
