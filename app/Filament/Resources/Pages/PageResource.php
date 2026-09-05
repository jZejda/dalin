<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages;

use App\Shared\Helpers\AppHelper;
use App\Enums\AppRoles;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\RichContentBlocks;
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
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PageResource extends Resource implements HasShieldPermissions
{
    public static ?int $navigationSort = 60;
    protected static ?string $model = Page::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.page.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('content.page.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.page.plural_label');
    }

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
                                ->live(onBlur: true)
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
                                            ->label(__('content.page.form.content'))
                                            ->customBlocks(RichContentBlocks::all())
                                            ->required()
                                            ->json()
                                            ->fileAttachmentsDisk('rich-editor-attachments')
                                            ->fileAttachmentsDirectory('attachments')
                                            ->fileAttachmentsVisibility('public')
//                                            ->saveUploadedFileAttachmentUsing(function ($file, $record) {
//                                                // If record doesn't exist yet (create mode), save temporarily
//                                                if (!$record) {
//                                                    $path = $file->store('attachments', 'rich-editor-attachments');
//                                                    return $path;
//                                                }
//
//                                                // Save file via medialibrary
//                                                // addMedia() accepts UploadedFile or string path
//                                                $media = $record->addMedia($file->getRealPath())
//                                                    ->usingName($file->getClientOriginalName())
//                                                    ->usingFileName($file->getClientOriginalName())
//                                                    ->toMediaCollection('rich-editor-attachments', 'rich-editor-attachments');
//
//                                                // Return media ID as identifier - we'll use it to retrieve URL later
//                                                // Format: "media:{id}" so we can distinguish it from regular paths
//                                                return 'media:' . $media->id;
//                                            })
//                                            ->getFileAttachmentUrlUsing(function ($file, $record) {
//                                                if (!$record) {
//                                                    // In create mode, return temporary URL
//                                                    return Storage::disk('rich-editor-attachments')->url($file);
//                                                }
//
//                                                // Check if file path is a media ID reference (format: "media:123")
//                                                if (str_starts_with($file, 'media:')) {
//                                                    $mediaId = (int) str_replace('media:', '', $file);
//                                                    $media = $record->getMedia('rich-editor-attachments')
//                                                        ->firstWhere('id', $mediaId);
//
//                                                    if ($media) {
//                                                        return $media->getUrl();
//                                                    }
//                                                }
//
//                                                // Try to find by path relative to root
//                                                $media = $record->getMedia('rich-editor-attachments')
//                                                    ->first(function ($media) use ($file) {
//                                                        return $media->getPathRelativeToRoot() === $file;
//                                                    });
//
//                                                // If not found by path, try by filename
//                                                if (!$media) {
//                                                    $fileName = basename($file);
//                                                    $media = $record->getMedia('rich-editor-attachments')
//                                                        ->firstWhere('file_name', $fileName);
//                                                }
//
//                                                if ($media) {
//                                                    return $media->getUrl();
//                                                }
//
//                                                // Fallback to direct storage URL
//                                                return Storage::disk('rich-editor-attachments')->url($file);
//                                            })
                                            ->toolbarButtons([
                                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'lead', 'link'],
                                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                                ['blockquote', 'code', 'codeBlock', 'highlight', 'bulletList', 'orderedList', 'details', 'grid', 'gridDelete'],
                                                ['table', 'attachFiles'],
                                                ['undo', 'redo', 'lead', 'small', 'textColor', 'customBlocks'],
                                            ])->floatingToolbars([
                                                'paragraph' => [
                                                    'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'code', 'grid', 'gridDelete', 'lead',
                                                ],
//                                                'heading' => [
//                                                    'h1', 'h2', 'h3',
//                                                ],
                                                'table' => [
                                                    'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                                                    'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                                                    'tableMergeCells', 'tableSplitCell',
                                                    'tableToggleHeaderRow',
                                                    'tableDelete',
                                                ],
                                            ])
                                    ];
                                }

                                // Defaultně Markdown (ContentFormat::Markdown = 2)
                                return [
                                    MarkdownEditor::make('content')
                                        ->label(__('content.page.form.content'))
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
                                ->label(__('content.page.form.author'))
                                ->options(User::query()->activeUsersByRole([AppRoles::Redactor])->pluck('name', 'id'))
                                ->default(Auth::id())
                                ->searchable()
                                ->required(),

                            Select::make('status')
                                ->options(PageStatus::class)
                                ->default(PageStatus::Closed)
                                ->selectablePlaceholder(),
                            Select::make('content_format')
                                ->label(__('content.page.form.format'))
                                ->options(ContentFormat::class)
                                ->default(ContentFormat::Markdown)
                                ->reactive()
                                ->disabled(fn ($context) => $context === 'edit')
                                ->required(),

                            Select::make('content_category_id')
                                ->label(__('content.page.form.category'))
                                ->options(ContentCategory::all()->pluck('title', 'id'))
                                ->searchable(),

                            Toggle::make('page_menu')->inline()
                                ->label(__('content.page.form.show_category_menu'))
                                ->onIcon('heroicon-s-check')
                                ->offIcon('heroicon-m-x-mark'),

                            TextInput::make('weight')
                                ->label(__('content.page.form.weight'))
                                ->maxValue(100)
                                ->minValue(0)
                                ->numeric()
                                ->default(50),

                            Repeater::make('meta_items')
                                ->label(__('content.page.form.meta'))
                                ->schema([
                                    Select::make('key')
                                        ->label(__('content.page.form.meta_key'))
                                        ->options([
                                            'title' => 'Title',
                                            'description' => 'Description',
                                            'keywords' => 'Keywords',
                                            'og:title' => 'OG Title',
                                            'og:description' => 'OG Description',
                                            'og:image' => 'OG Image',
                                        ])
                                        ->required()
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, $state, $get) {
                                            $currentItems = $get('../../meta_items') ?? [];
                                            $duplicates = collect($currentItems)
                                                ->where('key', $state)
                                                ->keys();

                                            if ($duplicates->count() > 1) {
                                                $set('key', null);
                                            }
                                        }),
                                    TextInput::make('value')
                                        ->label(__('content.page.form.meta_value'))
                                        ->required(),
                                ])
                                ->columns(2)
                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                ->defaultItems(0)
                                ->deletable(true)
                                ->addable(true)
                                ->reorderable(false)
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
                    ->label(__('content.page.table.title'))
                    ->description(fn (Page $record): HtmlString => new HtmlString('<a href="' . url('/stranka/' . $record->slug) . '"target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">' . ($record->slug ?? '') . '</a>'))
                    ->sortable()
                    ->searchable()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Medium),
                ViewColumn::make('user.name')
                    ->label(__('content.page.table.author'))
                    ->view('filament.tables.columns.user-identity')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('content_format')
                    ->label(__('content.page.table.format'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(AppHelper::DATE_TIME_FORMAT)
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
            __('content.page.search.author') => $record->user?->user_identification,
            __('content.page.search.category') => $record->content_category?->title,
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
