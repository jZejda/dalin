<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\AlertBlock;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\TextSize;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ViewPage;
use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageResource extends Resource implements HasShieldPermissions
{
    public static ?int $navigationSort = 60;
    protected static ?string $model = Page::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';
    protected static string | \UnitEnum | null $navigationGroup = 'Obsah';
    protected static ?string $label = 'Stránka';
    protected static ?string $pluralLabel = 'Stránky';

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
                                ->afterStateUpdated(function (Set $set, $state, $context) {
                                    if ($context === 'edit') {
                                        return;
                                    }

                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->rules(['alpha_dash'])
                                ->unique(ignoreRecord: true),

                            // Dynamic editor based on content_format
                            Grid::make(1)->schema(function (callable $get) {
                                $contentFormat = $get('content_format');

                                // Pokud je vybrán HTML (ContentFormat::Html = 1)
                                if ($contentFormat === 3 || $contentFormat === ContentFormat::TipTapJson) {
                                    return [
                                        RichEditor::make('content')
                                            ->label('Obsah')
                                            ->customBlocks([
                                                HeroBlock::class,
                                                AlertBlock::class,
                                            ])
                                            ->required()
                                            ->json()
                                            ->toolbarButtons([
                                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList', 'details', 'grid', 'gridDelete'],
                                                ['table', 'attachFiles'],
                                                ['undo', 'redo', 'lead', 'small', 'textColor', 'customBlocks'],
                                            ])
                                    ];
                                }

                                // Defaultně Markdown (ContentFormat::Markdown = 2)
                                return [
                                    MarkdownEditor::make('content')
                                        ->label('Obsah')
                                        ->required()
                                ];
                            })->columns(1)
                            ->columnSpan(2),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),


                    // Right Column
                    Section::make()
                        ->schema([
                            Select::make('user_id')
                                ->label('Author')
                                ->options(User::all()->pluck('name', 'id'))
                                ->default(Auth::id())
                                ->searchable()
                                ->required(),

                            Select::make('status')
                                ->options(PageStatus::class)
                                ->default(PageStatus::Closed)
                                ->selectablePlaceholder(),
                            Select::make('content_format')
                                ->label('Formát')
                                ->options(ContentFormat::class)
                                ->default(ContentFormat::Markdown)
                                ->reactive()
                                ->disabled(fn ($context) => $context === 'edit' || !Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                ->required(),

                            Select::make('content_category_id')
                                ->label('Kategorie')
                                ->options(ContentCategory::all()->pluck('title', 'id'))
                                ->searchable(),

                            Toggle::make('page_menu')->inline()
                                ->label('Zobrazit menu kategorie?')
                                ->onIcon('heroicon-s-check')
                                ->offIcon('heroicon-m-x-mark'),

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

                ])->columnSpan(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Název')
                    ->description(fn (Page $record): string => $record->slug ?? '')
                    ->sortable()
                    ->searchable()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Medium),
                TextColumn::make('user.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('content_format')
                    ->label('Formát obsahu')
                    ->badge()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime('d. m. Y - H:i')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->filters([
//                SelectFilter::make('user_id')->relationship('user_id', 'name'),
                SelectFilter::make('status')
                    ->options(PageStatus::class),
                    ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip(__('app.tables.actions_tooltip')),
            ])
            ->recordUrl(
                fn (Page $record): string => route('filament.admin.resources.pages.edit', ['record' => $record]),
            );
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
            'view' => ViewPage::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Page $record */
        return $record->title . ' | ' . $record->updated_at?->format('m. Y');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Page $record */
        return [
            'Autor' => $record->user?->user_identification,
            'Zařazeno' => $record->content_category?->title,
        ];
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
