<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventExports\Pages;

use App\Filament\Resources\SportEventExports\SportEventExportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSportEventExport extends CreateRecord
{
    protected static string $resource = SportEventExportResource::class;
}
