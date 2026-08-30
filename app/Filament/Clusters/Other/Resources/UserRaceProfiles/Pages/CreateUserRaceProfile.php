<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages;

use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRaceProfile extends CreateRecord
{
    protected static string $resource = UserRaceProfileResource::class;
}
