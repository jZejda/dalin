<?php

namespace App\Filament\Resources\UserCreditResource\Pages;

use App\Filament\Resources\UserCreditResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserCredits extends ListRecords
{
    protected static string $resource = UserCreditResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return UserCreditResource::getWidgets();
    }
}
