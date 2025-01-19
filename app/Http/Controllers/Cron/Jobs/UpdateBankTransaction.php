<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Services\Bank\BankAccountService;
use App\Services\Bank\Connector\MonetaBank;
use App\Services\Bank\Connector\Transaction;
use App\Services\Bank\MatchRules\ExtraMembershipFeesRule;
use App\Shared\Helpers\BankTransactionHelper;
use Carbon\Carbon;

final class UpdateBankTransaction implements CommonCronJobs
{
    public function run(): void
    {
        /** @var BankAccount[] $bankAccounts */
        $bankAccounts = BankAccount::query()->where('active', '=', 1)->get();

        foreach ($bankAccounts as $bankAccount) {
            $class = match ($bankAccount->code) {
                BankAccount::MONETA_MONEY_BANK => MonetaBank::class,
                default => null,
            };

            $bankTransactions = (new $class())->getTransactions($bankAccount, $bankAccount->last_synced);

            $this->storeTransactions($bankTransactions, $bankAccount->id);

            $bankAccount->last_synced = Carbon::now();
            $bankAccount->save();
        }
    }

    private function storeTransactions(array $bankTransactions, int $bankAccountId): void
    {
        /** @var Transaction[] $bankTransactions */
        foreach ($bankTransactions as $bankTransaction) {

            $transaction = BankTransaction::query()->where('external_key', '=', $bankTransaction->externalKey)->first();

            if ($transaction === null) {
                $transaction = new BankTransaction();
            }

            $transaction->bank_account_id = $bankAccountId;
            $transaction->transaction_indicator = $bankTransaction->transactionIndicator;
            $transaction->external_key = $bankTransaction->externalKey;
            $transaction->bank_account_identifier = $bankTransaction->bankAccountIdentifier;
            $transaction->date = $bankTransaction->dateTime;
            $transaction->amount = $bankTransaction->amount;
            $transaction->currency = $bankTransaction->currency;
            $transaction->variable_symbol = $bankTransaction->variableSymbol;
            $transaction->description = $bankTransaction->description;
            $transaction->note = $bankTransaction->note;

            $transaction->saveOrFail();

            if (!BankTransactionHelper::hasTransactionUserCredit($transaction)) {
                (new BankAccountService())->matchTransactionToUser($transaction, (new ExtraMembershipFeesRule())->getRule());
            }
        }
    }
}
