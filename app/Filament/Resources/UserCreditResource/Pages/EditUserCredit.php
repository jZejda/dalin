<?php

namespace App\Filament\Resources\UserCreditResource\Pages;

use App\Filament\Resources\UserCreditResource;
use App\Filament\Resources\UserCreditResource\Widgets\UserCreditOverview;
use App\Filament\Resources\UserCreditResource\Widgets\UserCreditChat;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserCredit extends EditRecord
{
    protected static string $resource = UserCreditResource::class;

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
           UserCreditChat::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            UserCreditOverview::class,
        ];
    }
}
