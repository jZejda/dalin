<?php

declare(strict_types=1);

namespace App\Services\Bank;

use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Mail\UserCreditChange;
use App\Models\BankTransaction;
use App\Models\User;
use App\Models\UserCredit;
use App\Services\Bank\Enums\TransactionIndicator;
use App\Services\Bank\MatchRules\CompareRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Str;

final class BankAccountService
{
    public function matchTransactionToUser(BankTransaction $transaction, CompareRule $compareRule): void
    {
        if (
            $transaction->transaction_indicator === TransactionIndicator::Credit
            && $compareRule->transactionIndicator === $transaction->transaction_indicator
        ) {

            $bankTransactionVariableSymbol = Str::trim($transaction->variable_symbol);

            if ($bankTransactionVariableSymbol !== null) {

                $searchingUserVariableSymbol = '';

                if ($compareRule->variablePrefix !== null) {
                    $prefixLength = strlen($compareRule->variablePrefix);
                    $searchingUserVariableSymbol = substr($bankTransactionVariableSymbol, $prefixLength);
                }

                $userByVariableSymbol = User::query()
                    ->where('payer_variable_symbol', '=', $searchingUserVariableSymbol)
                    ->where('active', '=', 1)
                    ->get();

                if (count($userByVariableSymbol) === 1) {
                    /** @var User $user */
                    $user = $userByVariableSymbol[0];
                    $userVariableSymbol = $compareRule->variablePrefix . $user->payer_variable_symbol;

                    if ($userVariableSymbol === $bankTransactionVariableSymbol) {
                        $userCredit = $this->storeCreditToUser($user, $transaction);
                        $this->sendUserEmail($user, $userCredit);
                    }
                }
            }
        }
    }

    private function storeCreditToUser(User $user, BankTransaction $transaction): UserCredit
    {
        $userCredit = new UserCredit();
        $userCredit->user_id = $user->id;
        $userCredit->amount = $transaction->amount;
        $userCredit->currency = $transaction->currency;
        $userCredit->bank_transaction_id = $transaction->id;
        $userCredit->source = UserCreditSource::User->value;
        $userCredit->status = UserCreditStatus::Done;
        $userCredit->credit_type = UserCreditType::UserDonation;
        $userCredit->source_user_id = 1;
        $userCredit->saveOrFail();

        Log::channel('site')->info('Uživateli '. $user->id .' byl automaticky přidán kredit: ' . $userCredit->id);

        return $userCredit;
    }

    private function sendUserEmail(User $user, UserCredit $userCredit): void
    {
        Mail::to($user)->queue(new UserCreditChange($user, $userCredit));
    }
}
