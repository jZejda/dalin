<?php

namespace App\Filament\Resources\UserCreditResource\Pages;

use App\Filament\Resources\UserCreditResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserCredit extends EditRecord
{
    protected static string $resource = UserCreditResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return UserCreditResource::getWidgets();
    }
}
