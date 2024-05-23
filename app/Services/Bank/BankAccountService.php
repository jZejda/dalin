<?php

declare(strict_types=1);

namespace App\Services\Bank;

use App\Models\BankAccount;
use App\Services\Bank\Connector\MonetaBank;

final class BankAccountService
{
    public function synchronizeAccount(): void
    {
        /** @var BankAccount[] $bankAccounts */
        $bankAccounts = BankAccount::query()->where('active', '=', 1)->get();

        foreach ($bankAccounts as $bankAccount) {
            $class = match ($bankAccount->code) {
                BankAccount::MONETA_MONEY_BANK => MonetaBank::class,
                default => null,
            };

            $list = (new $class())->getTransactions($bankAccount, $bankAccount->last_synced);

            dd($list);

        }

    }
}
