<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Services\Bank\BankAccountService;
use App\Services\Bank\Connector\Transaction;
use App\Services\Bank\MatchRules\ExtraMembershipFeesRule;
use App\Shared\Helpers\BankTransactionHelper;
use ValueError;

final class UpdateBankTransaction implements CommonCronJobs
{
    public function run(): void
    {
        if (!AppSetting::isBankModuleEnabled()) {
            Log::channel('site')->info('UpdateBankTransaction: bank module is disabled, skipping sync.');

            return;
        }

        /** @var BankAccount[] $bankAccounts */
        $bankAccounts = BankAccount::query()->where('active', '=', 1)->get();

        foreach ($bankAccounts as $bankAccount) {
            try {
                $connector = $bankAccount->code->connector();
            } catch (ValueError) {
                Log::channel('site')->warning("UpdateBankTransaction: unknown bank code '{$bankAccount->getRawOriginal('code')}' for account ID {$bankAccount->id}, skipping.");
                continue;
            }

            $bankTransactions = $connector->getTransactions($bankAccount, $bankAccount->last_synced?->subMinutes(5));

            if ($bankTransactions === null) {
                continue;
            }

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
