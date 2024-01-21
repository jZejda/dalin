<?php

namespace App\Filament\Resources\UserRaceProfileResource\Pages;

use App\Filament\Resources\UserRaceProfileResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserRaceProfile extends EditRecord
{
    protected static string $resource = UserRaceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
