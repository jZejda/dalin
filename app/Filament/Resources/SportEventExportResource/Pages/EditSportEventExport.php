<?php

namespace App\Filament\Resources\SportEventExportResource\Pages;

use App\Filament\Resources\SportEventExportResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSportEventExport extends EditRecord
{
    protected static string $resource = SportEventExportResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
