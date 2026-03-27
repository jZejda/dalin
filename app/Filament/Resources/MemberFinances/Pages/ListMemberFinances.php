<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances\Pages;

use App\Filament\Resources\MemberFinances\Actions\AddBulkCreditAction;
use App\Filament\Resources\MemberFinances\MemberFinanceResource;
use Filament\Actions\BulkAction;
use Filament\Resources\Pages\ListRecords;

class ListMemberFinances extends ListRecords
{
    protected static string $resource = MemberFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<BulkAction>
     */
    protected function getTableBulkActions(): array
    {
        return [
            AddBulkCreditAction::make(),
        ];
    }
}
