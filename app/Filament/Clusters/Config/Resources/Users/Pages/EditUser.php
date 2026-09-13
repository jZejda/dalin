<?php

namespace App\Filament\Clusters\Config\Resources\Users\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\Config\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
