<?php

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
