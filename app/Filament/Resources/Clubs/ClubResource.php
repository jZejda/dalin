<?php

namespace App\Filament\Resources\Clubs;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Clubs\Pages\ListClubs;
use App\Filament\Resources\Clubs\Pages\CreateClub;
use App\Filament\Resources\Clubs\Pages\EditClub;
use App\Models\Club;
use App\Models\SportRegion;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ClubResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Club::class;

    public static ?int $navigationSort = 11;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-identification';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.events');
    }

    public static function getNavigationLabel(): string
    {
        return __('club.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('club.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('club.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()->schema([
                            TextInput::make('abbr')
                                ->label(__('club.form.abbr'))
                                ->required(),
                            TextInput::make('name')
                                ->required(),
                            Select::make('region_id')
                                ->label(__('club.form.region'))
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
                TextColumn::make('abbr')
                    ->label(__('club.table.abbr'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('club.table.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('region.long_name')
                    ->label(__('club.table.region'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('oris_id')
                    ->label(__('club.table.oris_id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('oris_number')
                    ->label(__('club.table.oris_number'))
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('abbr')
            ->defaultPaginationPageOption(25)
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
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
            'index' => ListClubs::route('/'),
            'create' => CreateClub::route('/create'),
            'edit' => EditClub::route('/{record}/edit'),
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
