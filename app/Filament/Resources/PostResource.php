<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AppRoles;
use App\Filament\Resources\PostResource\Jobs\SendNewsMail;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PostResource extends Resource implements HasShieldPermissions
{
    public static ?int $navigationSort = 65;
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Obsah';
    protected static ?string $label = 'Novinka';
    protected static ?string $pluralLabel = 'Novinky';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('title')
                                ->label('Nadpis')
                                ->required(),
//                                ->reactive()
//                                ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
//                                    $set('slug', Str::slug($state));
//                                }),

                            // Markdown editor
                            Grid::make()->schema([
                                MarkdownEditor::make('content')
                                    ->label('Obsah novinky')
                                    ->required()
                            ])->columns(1),

                            Section::make('Dodatečné informace')
                                ->description('Editorial pro souhrn novinky - nepovinné - dostupné po rokliknutí')
                                ->schema([
                                    Grid::make()->schema([
                                        MarkdownEditor::make('editorial')
                                    ])->columns(1),
                                ])
                                ->collapsible()
                                ->persistCollapsed()
                                ->id('post-editorial'),
                        ])
                        ->columns(1)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),

                    // Right Column
                    Section::make()
                        ->schema([
                            Toggle::make('private')->inline()
                                ->label('Interní novinka')
                                ->onIcon('heroicon-m-bolt')
                                ->offIcon('heroicon-s-user')
                                ->default(true),
                            Select::make('user_id')
                                ->label('Author')
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable()
                                ->default(Auth::id())
                                ->required(),
                            Select::make('content_mode')
                                ->label('Formát')
                                ->options(
                                    [
//                                        1 => 'HTML',
                                        2 => 'Markdown',
                                    ]
                                )->default(2)
                                ->required(),
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
                    ->size(TextColumn\TextColumnSize::Large)
                    ->weight(FontWeight::Medium),
                TextColumn::make('user.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content_mode')
                    ->label('Formát obsahu')
                    ->badge()
                    ->sortable(),
                IconColumn::make('private')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime('d. m. Y - H:i')
                    ->sortable(),
                TextColumn::make('private')
                    ->badge()
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    self::sendNewsEmail(),
                    //DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip(__('app.tables.actions_tooltip')),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Post $record */
        return $record->title . ' | ' . $record->updated_at->format('m. Y');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Post $record */
        return [
            'Autor' => $record->user?->user_identification ?? 'N/A',
            'Stav' => ($record->private === true) ? 'Neveřejná' : 'Veřejná',
        ];
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => Pages\ViewPost::route('/{record}'),
        ];
    }

    public static function sendNewsEmail(): StaticAction
    {

        return Action::make('sendNewsEmail')
            ->action(function (array $data, Post $record): void {
                (new SendNewsMail(
                    $record,
                    $data['subject'],
                    $data['selection'],
                ))->send();

                Notification::make()
                    ->title('E-mail novinky rozeslán')
                    ->body('Zvoleným uživatelům byl odeslán e-mail.')
                    ->success()
                    ->seconds(8)
                    ->send();
            })
            ->color('gray')
            ->label('Pošli e-mail')
            ->icon('heroicon-s-paper-airplane')
            ->modalHeading('Pošle e-mailovou zprávu k novice')
            ->modalDescription('E-mail je odeslán sepárátně každému uživateli zvlášť. Pokud zvolíte zaslat zprávu všem, bude tato odeslána bez ohledu na uživatelské preferenci.')
            ->modalSubmitActionLabel('Odeslat')
            ->visible(function (): bool {
                $allowSendEmail = auth()->user()?->hasRole([AppRoles::SuperAdmin->value, AppRoles::Redactor->value]);
                if ($allowSendEmail === true) {
                    return true;
                }

                return false;
            })
            ->form([
                Grid::make(1)
                    ->schema([
                        TextInput::make('subject')
                            ->label('Předmět zprávy')
                            ->default(fn (Post $record): string => $record->title)
                            ->required(),
                        Select::make('selection')
                            ->label('Zvolte možnost')
                            ->options([
                                1 => 'Uživatelé kteří mají zájem o novinky',
                                0 => 'Všem aktivním uživatelům systému'
                            ])
                            ->default(1)
                            ->required(),

                    ]),

            ]);
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
