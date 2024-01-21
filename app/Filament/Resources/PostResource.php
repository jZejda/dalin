<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostResource extends Resource
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
                    // Main column
                    Section::make()
                        ->schema([

                            TextInput::make('title')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            Grid::make()->schema([
                                MarkdownEditor::make('editorial')
                                    ->maxLength(255),
                            ])->columns(1),

                            // Markdown editor
                            Grid::make()->schema([
                                MarkdownEditor::make('content')
                                    ->required()
                            ])->columns(1),
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
                                ->onIcon('heroicon-m-bolt')
                                ->offIcon('heroicon-s-user'),
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
                                        1 => 'HTML',
                                        2 => 'Markdown',
                                    ]
                                )->default(2)
                                ->disabled(!Auth::user()?->hasRole('super_admin'))
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
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-horizontal')
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

}
