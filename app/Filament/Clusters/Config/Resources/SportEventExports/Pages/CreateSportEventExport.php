<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\SportEventExports\Pages;

use App\Filament\Clusters\Config\Resources\SportEventExports\SportEventExportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSportEventExport extends CreateRecord
{
    protected static string $resource = SportEventExportResource::class;
}
