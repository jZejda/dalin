<?php

namespace App\Filament\Resources\SportClassDefinitions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\SportClassDefinitions\SportClassDefinitionResource;
use Filament\Resources\Pages\EditRecord;

class EditSportClassDefinition extends EditRecord
{
    protected static string $resource = SportClassDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
