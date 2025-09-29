<?php

namespace App\Filament\Resources\UserEntries\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\UserEntries\UserEntryResource;
use Filament\Resources\Pages\EditRecord;

class EditUserEntry extends EditRecord
{
    protected static string $resource = UserEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
