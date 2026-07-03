<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Transport\Resources\Vehicles\Pages;

use App\Filament\Clusters\Transport\Resources\Vehicles\VehicleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Klubové vozidlo nemá vlastníka.
        $data['user_id'] = null;

        return $data;
    }
}
