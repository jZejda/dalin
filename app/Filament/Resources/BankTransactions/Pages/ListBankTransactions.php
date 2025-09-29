<?php

namespace App\Filament\Resources\BankTransactions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\BankTransactions\BankTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListBankTransactions extends ListRecords
{
    protected static string $resource = BankTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
