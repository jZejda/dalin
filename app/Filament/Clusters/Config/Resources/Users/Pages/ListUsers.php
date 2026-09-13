<?php

namespace App\Filament\Clusters\Config\Resources\Users\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Config\Resources\Users\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
