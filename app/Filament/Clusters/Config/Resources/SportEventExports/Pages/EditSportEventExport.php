<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\SportEventExports\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\Config\Resources\SportEventExports\SportEventExportResource;
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
