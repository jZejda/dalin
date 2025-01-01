<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Mail\UserCreditAdd;
use App\Models\BankTransaction;
use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class UserCreditService
{
    public function addCredit(User $user, BankTransaction $bankTransaction): void
    {
        if ($bankTransaction->variable_symbol !== null) {
            $users = User::query()
                ->where('payer_variable_symbol', '=', $bankTransaction->variable_symbol)
                ->where('active', '=', 1)
                ->get();

            if ($users !== null && count($users) === 1) {

                $userCredit = new UserCredit();
                $userCredit->user_id = $user->id;
                $userCredit->amount = $bankTransaction->amount;
                $userCredit->currency = $bankTransaction->currency;
                $userCredit->bank_transaction_id = $bankTransaction->id;
                $userCredit->source = UserCreditSource::System->value;
                $userCredit->status = UserCreditStatus::Done;
                $userCredit->credit_type = UserCreditType::UserDonation;
                $userCredit->source_user_id = 1;
                $userCredit->saveOrFail();

                Log::channel('site')->info(sprintf('Bank Transaction ID %d byla přidána uživateli ID:  %d', $bankTransaction->id, $user->id));
            }
        }
    }

    public function removeCredit(int $userId, int $amount): void
    {
        // Remove credit from user
    }

    public function sendUserNotification(User $user, UserCredit $userCredit): void
    {
        if (true) {
            Mail::to($user)
            ->queue(new UserCreditAdd($userCredit->amount, $userCredit->currency, $user));
        }
    }

}
