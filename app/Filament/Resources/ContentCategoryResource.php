<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentCategoryResource\Pages;
use App\Filament\Resources\ContentCategoryResource\RelationManagers;
use App\Models\ContentCategory;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ContentCategoryResource extends Resource
{
    protected static ?int $navigationSort = 20;
    protected static ?string $model = ContentCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Obsah';
    protected static ?string $label = 'Kategorie';
    protected static ?string $pluralLabel = 'Kategorie';

    public static function form(Form $form): Form
    {
        Section::make('Vysvětlivka')
            ->description('Pokud ')
            ->schema([]);

        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    Section::make('Kategorie')
                        ->description('Kategorie slouží ke strukturování obsahu stránek.
                        Například umožní vytvářet stránky jednoho závodu které spolu souvísí.
                        Stránky v jedné kategorii mohou zobrazovat menu osatatních stránek v kategorii.')
                        ->schema([
                            TextInput::make('title')->required(),
                            TextInput::make('slug'),
                            Grid::make()
                                ->schema([
                                    TextInput::make('description'),
                                ])
                                ->columns(1),
                        ])
                        ->columns(2)
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
                TextColumn::make('title')->label(__('filament-admin.content_category.title')),
                TextColumn::make('description')->limit(50),
                TextColumn::make('slug'),
            ])
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

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title;
    }
}
