<?php

namespace App\Filament\Resources\UserRaceProfileResource\Pages;

use App\Filament\Resources\UserRaceProfileResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserRaceProfiles extends ListRecords
{
    protected static string $resource = UserRaceProfileResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
