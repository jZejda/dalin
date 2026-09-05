<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Pages;

use App\Filament\Clusters\Config\Resources\Vehicles\VehicleResource;
use App\Filament\Clusters\Other\OtherCluster;
use App\Models\AppSetting;
use App\Models\Vehicle;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MyVehicleList extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::Car;

    protected static ?string $cluster = OtherCluster::class;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.clusters.other.pages.my-vehicle-list';

    public static function getNavigationLabel(): string
    {
        return __('vehicle.my_vehicles');
    }

    public function getTitle(): string
    {
        return __('vehicle.my_vehicles');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && AppSetting::isTransportModuleEnabled();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Vehicle::query()->ownedBy((int) Auth::id()))
            ->columns(VehicleResource::vehicleTableColumns())
            ->defaultSort('name')
            ->headerActions([
                CreateAction::make()
                    ->model(Vehicle::class)
                    ->label(__('vehicle.add_vehicle'))
                    ->modalHeading(__('vehicle.add_vehicle'))
                    ->icon('heroicon-o-plus')
                    ->schema(VehicleResource::vehicleFormComponents())
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(VehicleResource::vehicleFormComponents()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading(__('vehicle.empty_my_vehicles'))
            ->emptyStateDescription(__('vehicle.empty_my_vehicles_description'))
            ->emptyStateIcon('heroicon-o-truck');
    }
}
