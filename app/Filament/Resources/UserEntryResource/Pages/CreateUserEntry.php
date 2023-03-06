<?php

namespace App\Filament\Resources\UserEntryResource\Pages;

use App\Filament\Resources\UserEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserEntry extends CreateRecord
{
    protected static string $resource = UserEntryResource::class;
}
