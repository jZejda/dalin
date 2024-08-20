<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Filament\Resources\PageResource\Pages;
use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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

                            // Markdown editor
                            Grid::make()->schema([
                                MarkdownEditor::make('content')
                                ->label('Obsah')
                                ->required()
                            ])->columns(1),
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
                                ->disabled(!Auth::user()?->hasRole('super_admin'))
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

                ])
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
                    ->size(TextColumn\TextColumnSize::Large)
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
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-horizontal')
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
            'view' => Pages\ViewPage::route('/{record}'),
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
