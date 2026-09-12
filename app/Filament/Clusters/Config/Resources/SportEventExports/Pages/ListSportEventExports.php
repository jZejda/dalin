<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\SportEventExports\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Config\Resources\SportEventExports\SportEventExportResource;
use Filament\Resources\Pages\ListRecords;

class ListSportEventExports extends ListRecords
{
    protected static string $resource = SportEventExportResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
