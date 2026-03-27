<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances\Pages;

use App\Filament\Resources\MemberFinances\Actions\AddMemberCreditAction;
use App\Filament\Resources\MemberFinances\MemberFinanceResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMemberFinance extends ViewRecord
{
    protected static string $resource = MemberFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AddMemberCreditAction::deposit(),
            AddMemberCreditAction::deduct(),
        ];
    }
}
