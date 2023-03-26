<?php

namespace App\Filament\Resources\UserEntryResource\Pages;

use App\Filament\Resources\UserEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserEntries extends ListRecords
{
    protected static string $resource = UserEntryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
