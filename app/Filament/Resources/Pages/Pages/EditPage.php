<?php

namespace App\Filament\Resources\Pages\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Pages\PageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
