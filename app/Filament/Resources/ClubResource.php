<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubResource\Pages;
use App\Models\Club;
use App\Models\SportRegion;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    public static ?int $navigationSort = 9;
    protected static ?string $navigationGroup = 'Akce/Závody';
    protected static ?string $label = 'Klub';
    protected static ?string $pluralLabel = 'Kluby';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make()->schema([
                            TextInput::make('abbr')
                                ->label('Zkratka')
                                ->required(),
                            TextInput::make('name')
                                ->required(),
                            Select::make('region_id')
                                ->label('Region')
                                ->options(SportRegion::all()->pluck('long_name', 'id'))
                                ->searchable()
                                ->required(),
                        ])->columns(3),
                        TextInput::make('oris_id')
                            ->disabled(true),
                        TextInput::make('oris_number')
                            ->disabled(true),
                    ])
                    ->columns(2)
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 12
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('abbr')
                    ->label('Zkratka')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Název')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('region.long_name')
                    ->label('Region')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('oris_id')
                    ->label('ORIS ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('oris_number')
                    ->label('ORIS Number')
                    ->sortable()
                    ->searchable(),
            ])->defaultSort('abbr')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClubs::route('/'),
            'create' => Pages\CreateClub::route('/create'),
            'edit' => Pages\EditClub::route('/{record}/edit'),
        ];
    }
}
