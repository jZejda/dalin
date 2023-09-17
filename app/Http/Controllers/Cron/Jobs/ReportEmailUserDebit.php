<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Enums\AppRoles;
use App\Mail\UsersInDebit;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportEmailUserDebit implements CommonCronJobs
{
    public function run(): void
    {
        $users = User::role(AppRoles::BillingSpecialist->value)->get();
        foreach ($users as $user) {
            Mail::to($user)->send(new UsersInDebit());
            Log::channel('site')->info('MailMonthlyUserDebitReport mail for user: ' . $user->email . ' - ' . $user->name);
        }
        Log::channel('site')->info('Report User Debit was send');
    }
}
