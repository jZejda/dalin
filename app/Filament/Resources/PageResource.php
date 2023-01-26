<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Obsah';
    protected static ?string $label = 'Stránka';
    protected static ?string $pluralLabel = 'Stránky';

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

                            // Markdown editor
                            Grid::make()->schema([
                                RichEditor::make('content')

                            ])->columns(1),

                            // File Upload
                            Grid::make()->schema([
                                FileUpload::make('picture_attachment')
                                    ->directory('pages')
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
                            Select::make('user_id')
                                ->label('Author')
                                ->options(User::all()->pluck('name', 'id'))
                                ->default(Auth::id())
                                ->searchable(),

                            Select::make('status')
                                ->options([
                                    Page::STATUS_OPEN => 'Zveřejněno',
                                    Page::STATUS_CLOSED => 'Neaktivní',
                                    Page::STATUS_DRAFT => 'Rozpracováno',
                                    Page::STATUS_ARCHIVE => 'Archiv'
                                ])
                                ->default(Page::STATUS_CLOSED)
                                ->disablePlaceholderSelection(),

                            Select::make('content_format')
                                ->label('Formát')
                                ->options([
                                        1 => 'Markdown',
                                        2 => 'HTML',
                                    ]
                                )->default(1),

                            Select::make('content_category_id')
                                ->label('Kategorie')
                                ->options(ContentCategory::all()->pluck('title', 'id'))
                                ->searchable(),

                            Select::make('status')
                                ->options(self::getPageStatuses())
                                ->default(Page::STATUS_CLOSED)
                                ->disablePlaceholderSelection(),

                            Toggle::make('page_menu')->inline()
                                ->label('Zobrazit menu kategorie?')
                                ->onIcon('heroicon-s-check')
                                ->offIcon('heroicon-s-x'),

                            TextInput::make('weight')
                                ->label('Váha')
                                ->maxValue(100)
                                ->minValue(0)
                                ->numeric()
                                ->default(50),

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
                TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime('d. m. Y - H:i'),
            ])
            ->filters([
//                SelectFilter::make('user_id')->relationship('user_id', 'name'),
                SelectFilter::make('status')
                    ->options(self::getPageStatuses()),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
            'view' => Pages\ViewPage::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'content'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Page $record */
        return $record->title . ' | ' . $record->updated_at->format('m. Y');
    }

    private static function getPageStatuses(): array
    {
        return [
            Page::STATUS_OPEN => 'Zveřejněno',
            Page::STATUS_CLOSED => 'Neaktivní',
            Page::STATUS_DRAFT => 'Rozpracováno',
            Page::STATUS_ARCHIVE => 'Archiv'
        ];
    }
}
