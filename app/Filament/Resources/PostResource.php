<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Card;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function PHPUnit\Framework\stringContains;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Obsah';

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
                            Grid::make()->schema([
                                RichEditor::make('editorial')
                                    ->required(),
                            ])->columns(1),

                            // Markdown editor
                            Grid::make()->schema([
                                RichEditor::make('content')
                            ])->columns(1),


                        ])
                        ->columns(1)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),


                    // Right Column
                    Card::make()
                        ->schema([
                            Toggle::make('private')->inline()
                                ->onIcon('heroicon-s-lightning-bolt')
                                ->offIcon('heroicon-s-user'),
                            Select::make('user_id')
                                ->label('Author')
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable()
                                ->default(Auth::id()),
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
                TextColumn::make('title'),
                IconColumn::make('private')
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime('d. m. Y - H:i')
                    ->sortable(),
                BadgeColumn::make('private')
                    ->enum([
                        0 => 'veřejná',
                        1 => 'neveřejná',
                    ])
                    ->colors([
                        0 => 'warning',
                        1 => 'primary',
                    ]),

            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => Pages\ViewPost::route('/{record}'),
        ];
    }

}
