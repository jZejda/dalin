<?php

namespace App\Filament\Resources\UserEntryResource\Pages;

use App\Filament\Resources\UserEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserEntry extends EditRecord
{
    protected static string $resource = UserEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
