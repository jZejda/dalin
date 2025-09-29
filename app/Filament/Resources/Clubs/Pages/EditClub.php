<?php

namespace App\Filament\Resources\Clubs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Clubs\ClubResource;
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
