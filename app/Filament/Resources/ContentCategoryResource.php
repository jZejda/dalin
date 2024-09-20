<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\SportEventType;
use App\Filament\Resources\ContentCategoryResource\Pages;
use App\Filament\Resources\ContentCategoryResource\RelationManagers;
use App\Models\ContentCategory;
use App\Models\SportEvent;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Obsah';
    protected static ?string $label = 'Kategorie';
    protected static ?string $pluralLabel = 'Kategorie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                                ->afterStateUpdated(function (\Filament\Forms\Set $set, $state, $context) {
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
                                ->columns(1),
                        ])
                        ->columns(3)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 12,
                        ]),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament/common.content_category.title'))
                    ->size(TextColumn\TextColumnSize::Large)
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
            RelationManagers\PageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentCategories::route('/'),
            'create' => Pages\CreateContentCategory::route('/create'),
            'edit' => Pages\EditContentCategory::route('/{record}/edit'),
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
