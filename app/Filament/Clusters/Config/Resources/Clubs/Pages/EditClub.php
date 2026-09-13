<?php

namespace App\Filament\Clusters\Config\Resources\Clubs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\Config\Resources\Clubs\ClubResource;
use Filament\Resources\Pages\EditRecord;

class EditClub extends EditRecord
{
    protected static string $resource = ClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
