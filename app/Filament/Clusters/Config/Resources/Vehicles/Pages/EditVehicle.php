<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\Vehicles\Pages;

use App\Filament\Clusters\Config\Resources\Vehicles\VehicleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
