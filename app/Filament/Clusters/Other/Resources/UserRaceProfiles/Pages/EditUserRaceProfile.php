<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditUserRaceProfile extends EditRecord
{
    protected static string $resource = UserRaceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
