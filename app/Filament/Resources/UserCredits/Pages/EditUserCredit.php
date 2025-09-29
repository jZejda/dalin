<?php

namespace App\Filament\Resources\UserCredits\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\UserCredits\UserCreditResource;
use App\Filament\Resources\UserCredits\Widgets\UserCreditOverview;
use App\Filament\Resources\UserCredits\Widgets\UserCreditChat;
use Filament\Resources\Pages\EditRecord;

class EditUserCredit extends EditRecord
{
    protected static string $resource = UserCreditResource::class;

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
           UserCreditChat::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getFooterWidgetsColumns(): int|array
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
