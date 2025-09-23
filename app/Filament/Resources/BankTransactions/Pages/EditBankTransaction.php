<?php

namespace App\Filament\Resources\BankTransactions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\BankTransactions\BankTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankTransaction extends EditRecord
{
    protected static string $resource = BankTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
