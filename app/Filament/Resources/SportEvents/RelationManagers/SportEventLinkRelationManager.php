<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use App\Enums\SportEventLinkType;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\SportEventLink;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SportEventLinkRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventLinks';

    protected static ?string $recordTitleAttribute = 'name_cz';

    private const string KIND_URL = 'url';

    private const string KIND_FILE = 'file';

    /** 10 MB — stays under Livewire's default temporary upload limit (12 MB). */
    private const int MAX_FILE_SIZE_KB = 10240;

    /**
     * Accepted MIME type (detected from the file content) => extension the file is stored with.
     * Documents and images only: the events disk is public, so nothing a browser or the web
     * server would execute (HTML, SVG, JS, PHP) may be uploaded — and the client-supplied
     * extension is never trusted, see storedFileName().
     *
     * @var array<string, string>
     */
    private const array ACCEPTED_FILE_TYPES = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'text/plain' => 'txt',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.oasis.opendocument.text' => 'odt',
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
    ];

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_links.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('sport-event.relation_links.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_cz')
                    ->label(__('sport-event.relation_links.name_cz'))
                    ->required(),
                TextInput::make('name_en')
                    ->label(__('sport-event.relation_links.name_en'))
                    ->required(),
                TextInput::make('description_cz')
                    ->label(__('sport-event.relation_links.description_cz')),
                TextInput::make('description_en')
                    ->label(__('sport-event.relation_links.description_en')),
                ToggleButtons::make('link_kind')
                    ->label(__('sport-event.relation_links.link_kind'))
                    ->options([
                        self::KIND_URL => __('sport-event.relation_links.link_kind_options.url'),
                        self::KIND_FILE => __('sport-event.relation_links.link_kind_options.file'),
                    ])
                    ->icons([
                        self::KIND_URL => LucideIcon::Link,
                        self::KIND_FILE => LucideIcon::Upload,
                    ])
                    ->inline()
                    ->live()
                    ->default(self::KIND_URL)
                    ->afterStateHydrated(function (ToggleButtons $component, ?SportEventLink $record): void {
                        // ->default() does not run when editing, derive the kind from the record
                        if ($record !== null) {
                            $component->state($record->isUploadedFile() ? self::KIND_FILE : self::KIND_URL);
                        }
                    })
                    // ORIS sync rewrites its links to URLs, so they can't become uploaded files
                    ->disabled(fn (?SportEventLink $record): bool => $record?->external_key !== null)
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Grid::make()->columnSpanFull()->schema([
                    TextInput::make('source_url')
                        ->label(__('sport-event.relation_links.source_url'))
                        ->url()
                        ->maxLength(512)
                        ->required()
                        ->visible(fn (Get $get): bool => $get('link_kind') !== self::KIND_FILE),
                    FileUpload::make('source_path')
                        ->label(__('sport-event.relation_links.source_file'))
                        ->helperText(__('sport-event.relation_links.source_file_helper'))
                        ->disk(SportEventLink::FILE_DISK)
                        ->directory(fn (): string => SportEventLink::FILE_DIRECTORY.'/'.$this->getOwnerRecord()->getKey())
                        ->visibility('public')
                        ->acceptedFileTypes(array_keys(self::ACCEPTED_FILE_TYPES))
                        ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file): string => self::storedFileName($file))
                        ->maxSize(self::MAX_FILE_SIZE_KB)
                        ->openable()
                        ->downloadable()
                        ->required()
                        ->visible(fn (Get $get): bool => $get('link_kind') === self::KIND_FILE),
                ])->columns(1),
                Select::make('source_type')
                    ->label(__('sport-event.relation_links.source_type'))
                    ->required()
                    ->options(SportEventLinkType::enumArray()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('source')
                    ->label(__('sport-event.relation_links.table.source'))
                    ->state(fn (SportEventLink $record) => $record->source())
                    ->tooltip(fn (SportEventLink $record): string => $record->source()->getLabel())
                    ->color('gray'),
                TextColumn::make('name_cz')
                    ->label(__('sport-event.relation_links.table.name_cz'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label(__('sport-event.relation_links.table.name_en'))
                    ->sortable(),
                TextColumn::make('source_url')
                    ->label(__('sport-event.relation_links.table.source_url'))
                    ->state(fn (SportEventLink $record): ?string => $record->url())
                    ->url(fn (SportEventLink $record): ?string => $record->url(), shouldOpenInNewTab: true)
                    ->limit(60),
            ])
            ->filters([
                //
            ])
            ->headerActions(self::buttonCreateActionVisibility())
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->mutateDataUsing(fn (array $data): array => self::normalizeTarget($data)),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function buttonCreateActionVisibility(): array
    {
        // TODO z recordu nejak vytahnout jestli je oris nebo ne a pak to skryt
        return [
            CreateAction::make()
                ->mutateDataUsing(fn (array $data): array => self::normalizeTarget($data)),
        ];
    }

    /**
     * "<slug of the original name>-<ulid>.<extension by detected MIME type>": readable when
     * downloaded, unique, and never e.g. "x.php" for a file whose content passes as text/plain.
     */
    private static function storedFileName(TemporaryUploadedFile $file): string
    {
        $extension = self::ACCEPTED_FILE_TYPES[(string) $file->getMimeType()] ?? 'bin';
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return Str::limit($name !== '' ? $name : 'soubor', 60, '').'-'.Str::lower((string) Str::ulid()).'.'.$extension;
    }

    /**
     * A link points either to an uploaded file or to an external URL, never both;
     * the hidden field of the other kind is not dehydrated, so clear it explicitly.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function normalizeTarget(array $data): array
    {
        $isFile = filled($data['source_path'] ?? null);

        $data['source_path'] = $isFile ? $data['source_path'] : null;
        $data['source_url'] = $isFile ? null : ($data['source_url'] ?? null);
        $data['internal'] = $isFile;

        return $data;
    }
}
