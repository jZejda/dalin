<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Transport\Resources\Vehicles;

use App\Filament\Clusters\Transport\Resources\Vehicles\Pages\CreateVehicle;
use App\Filament\Clusters\Transport\Resources\Vehicles\Pages\EditVehicle;
use App\Filament\Clusters\Transport\Resources\Vehicles\Pages\ListVehicles;
use App\Filament\Clusters\Transport\TransportCluster;
use App\Models\Vehicle;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VehicleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $cluster = TransportCluster::class;

    protected static ?int $navigationSort = 20;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Klubová vozidla';

    protected static ?string $label = 'Klubové vozidlo';

    protected static ?string $pluralLabel = 'Klubová vozidla';

    /** Resource spravuje pouze klubová vozidla (user_id IS NULL). */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('user_id');
    }

    public static function canCreate(): bool
    {
        // Policy create je otevřená kvůli vlastním vozidlům členů,
        // klubová vozidla smí zakládat jen role s oprávněním.
        return Auth::user()?->can('Create:Vehicle') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema(self::vehicleFormComponents())
                ->columnSpan('full'),
        ]);
    }

    /**
     * Sdílené formulářové komponenty vozidla (použito i na stránce Moje vozidla).
     *
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function vehicleFormComponents(): array
    {
        return [
            Grid::make()->schema([
                TextInput::make('name')
                    ->label(__('vehicle.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('brand')
                    ->label(__('vehicle.brand'))
                    ->maxLength(255),
            ])->columns(2),
            Grid::make()->schema([
                TextInput::make('seats')
                    ->label(__('vehicle.seats'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->required(),
                TextInput::make('consumption')
                    ->label(__('vehicle.consumption'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix(__('vehicle.consumption_suffix')),
                TextInput::make('price_per_km')
                    ->label(__('vehicle.price_per_km'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix(__('vehicle.price_per_km_suffix')),
            ])->columns(3),
            TextInput::make('operator')
                ->label(__('vehicle.operator'))
                ->maxLength(255),
            Textarea::make('description')
                ->label(__('vehicle.description'))
                ->rows(3),
            Toggle::make('active')
                ->label(__('vehicle.active'))
                ->default(true),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::vehicleTableColumns())
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('active')
                    ->label(__('vehicle.active')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * Sdílené sloupce tabulky vozidel (použito i na stránce Moje vozidla).
     *
     * @return array<int, \Filament\Tables\Columns\Column>
     */
    public static function vehicleTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('vehicle.name'))
                ->sortable()
                ->searchable(),
            TextColumn::make('brand')
                ->label(__('vehicle.brand'))
                ->sortable()
                ->searchable()
                ->placeholder('—'),
            TextColumn::make('seats')
                ->label(__('vehicle.seats'))
                ->sortable(),
            TextColumn::make('operator')
                ->label(__('vehicle.operator'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('consumption')
                ->label(__('vehicle.consumption'))
                ->suffix(' '.__('vehicle.consumption_suffix'))
                ->placeholder('—'),
            TextColumn::make('price_per_km')
                ->label(__('vehicle.price_per_km'))
                ->suffix(' '.__('vehicle.price_per_km_suffix'))
                ->placeholder('—'),
            IconColumn::make('active')
                ->label(__('vehicle.active'))
                ->boolean(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'edit' => EditVehicle::route('/{record}/edit'),
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
            'delete_any',
        ];
    }
}
