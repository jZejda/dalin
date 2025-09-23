<?php

namespace App\Filament\Resources\SportEventExports\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SportEventExports\SportEventExportResource;
use Filament\Pages\Actions;
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
