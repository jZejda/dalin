<?php

namespace App\Filament\Resources\UserEntries\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\UserEntries\UserEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserEntries extends ListRecords
{
    protected static string $resource = UserEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
