<?php

namespace App\Filament\Resources\SportEventExports\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\SportEventExports\SportEventExportResource;
use Filament\Resources\Pages\EditRecord;

class EditSportEventExport extends EditRecord
{
    protected static string $resource = SportEventExportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
