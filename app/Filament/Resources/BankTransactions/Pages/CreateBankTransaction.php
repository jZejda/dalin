<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankTransactions\Pages;

use App\Filament\Resources\BankTransactions\BankTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankTransaction extends CreateRecord
{
    protected static string $resource = BankTransactionResource::class;
}
