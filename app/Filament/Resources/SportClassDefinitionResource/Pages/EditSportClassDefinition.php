<?php

namespace App\Filament\Resources\SportClassDefinitionResource\Pages;

use App\Filament\Resources\SportClassDefinitionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSportClassDefinition extends EditRecord
{
    protected static string $resource = SportClassDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
