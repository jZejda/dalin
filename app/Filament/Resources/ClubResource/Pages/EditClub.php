<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClub extends EditRecord
{
    protected static string $resource = ClubResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
