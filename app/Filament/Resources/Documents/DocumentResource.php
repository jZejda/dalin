<?php

declare(strict_types=1);

namespace App\Filament\Resources\Documents;

use App\Enums\DocumentCategory;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;

class DocumentResource extends Resource implements HasShieldPermissions
{
    protected static ?int $navigationSort = 67;

    protected static ?string $model = Document::class;

    protected static string|\BackedEnum|null $navigationIcon = LucideIcon::FolderOpen;

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.content');
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('document.navigation_label');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('document.label');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('document.plural_label');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['sm' => 1, 'md' => 12])->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('title')
                                ->label(__('document.form.title'))
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                    // The slug is the permanent URL, so it is only derived once
                                    if ($operation === 'create') {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),
                            TextInput::make('slug')
                                ->label(__('document.form.slug'))
                                ->helperText(__('document.form.slug_helper'))
                                ->prefix(url('/dokumenty').'/')
                                ->required()
                                ->maxLength(255)
                                ->rules(['alpha_dash'])
                                ->unique(ignoreRecord: true),
                            Textarea::make('description')
                                ->label(__('document.form.description'))
                                ->helperText(__('document.form.description_helper'))
                                ->rows(3)
                                ->maxLength(1000),
                            SpatieMediaLibraryFileUpload::make('file')
                                ->label(__('document.form.file'))
                                ->helperText(__('document.form.file_helper', ['max' => Document::MAX_FILE_SIZE_KB / 1024]))
                                ->collection(Document::MEDIA_COLLECTION_FILE)
                                ->disk(Document::DISK)
                                ->visibility('private')
                                ->acceptedFileTypes(Document::ACCEPTED_MIME_TYPES)
                                ->maxSize(Document::MAX_FILE_SIZE_KB)
                                ->preserveFilenames()
                                ->downloadable()
                                ->required(),
                        ])
                        ->columnSpan(['sm' => 1, 'md' => 8]),

                    Section::make()
                        ->schema([
                            Select::make('category')
                                ->label(__('document.form.category'))
                                ->options(DocumentCategory::class)
                                ->default(DocumentCategory::AnnualReport)
                                ->required(),
                            TextInput::make('year')
                                ->label(__('document.form.year'))
                                ->helperText(__('document.form.year_helper'))
                                ->integer()
                                ->minValue(1950)
                                ->maxValue(2100)
                                ->default((int) date('Y')),
                            Toggle::make('is_public')
                                ->label(__('document.form.is_public'))
                                ->helperText(__('document.form.is_public_helper'))
                                ->default(true),
                            TextInput::make('sort')
                                ->label(__('document.form.sort'))
                                ->helperText(__('document.form.sort_helper'))
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                        ])
                        ->columnSpan(['sm' => 1, 'md' => 4]),
                ])->columnSpanFull(),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('document.table.title'))
                    ->weight(FontWeight::Medium)
                    ->description(fn (Document $record): string => '/dokumenty/'.$record->slug)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('document.table.category'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('year')
                    ->label(__('document.table.year'))
                    ->sortable(),
                IconColumn::make('is_public')
                    ->label(__('document.table.is_public'))
                    ->boolean()
                    ->trueIcon(LucideIcon::Globe)
                    ->falseIcon(LucideIcon::Lock)
                    ->falseColor('warning'),
                TextColumn::make('media.size')
                    ->label(__('document.table.size'))
                    ->state(fn (Document $record): ?string => $record->file()?->human_readable_size),
                ViewColumn::make('user.name')
                    ->label(__('document.table.uploaded_by'))
                    ->view('filament.tables.columns.user-badge'),
                TextColumn::make('updated_at')
                    ->label(__('document.table.updated_at'))
                    ->dateTime(AppHelper::DATE_TIME_FORMAT)
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['media', 'user']))
            ->defaultSort(fn ($query) => $query->orderBy('category')->ordered())
            ->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('category')
                    ->label(__('document.filters.category'))
                    ->options(DocumentCategory::class),
                TernaryFilter::make('is_public')
                    ->label(__('document.filters.is_public')),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('open')
                        ->label(__('document.actions.open.label'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Document $record): string => $record->publicUrl(), shouldOpenInNewTab: true)
                        ->visible(fn (Document $record): bool => $record->file() !== null),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip(__('app.tables.actions_tooltip')),
            ])
            ->emptyStateIcon(LucideIcon::FolderOpen)
            ->emptyStateHeading(__('document.table.empty_heading'))
            ->emptyStateDescription(__('document.table.empty_description'));
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description'];
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Document $record */
        return [
            __('document.table.category') => $record->category->getLabel(),
            __('document.table.year') => (string) ($record->year ?? '—'),
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
