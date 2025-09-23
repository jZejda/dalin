<?php

namespace App\Filament\Resources\Shield\Roles\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Shield\Roles\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
