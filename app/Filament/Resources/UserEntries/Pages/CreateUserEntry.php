<?php

namespace App\Filament\Resources\UserEntries\Pages;

use App\Filament\Resources\UserEntries\UserEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserEntry extends CreateRecord
{
    protected static string $resource = UserEntryResource::class;
}
