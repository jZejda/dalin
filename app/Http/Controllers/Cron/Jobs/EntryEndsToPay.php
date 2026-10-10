<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EntryEndsToPay implements CommonCronJobs
{
    public function run(): void
    {
        $deadlines = [1, 2, 3];

        foreach ($deadlines as $deadline) {

            $column = 'entry_date_'.$deadline;
            $sportEvents = SportEvent::query()
                ->with('sportDiscipline')
                ->whereNotNull($column)
                ->whereHas('userEntry', static fn (Builder $query): Builder => $query->where('entry_status', '!=', EntryStatus::Cancel->value))
                ->where($column, '<=', Carbon::now()->endOfHour()->format(AppHelper::MYSQL_DATE_TIME))
                ->where($column, '>=', Carbon::now()->startOfHour()->format(AppHelper::MYSQL_DATE_TIME))
                ->get();

            if ($sportEvents->isNotEmpty()) {
                $users = User::role(AppRoles::BillingSpecialist->value)
                    ->where('active', '=', 1)
                    ->get();

                foreach ($users as $user) {
                    Mail::to($user)->send(new \App\Mail\EntryEndsToPay($sportEvents, $deadline));
                    Log::channel('site')->info('MailEntryEndsToPay mail for user: ' . $user->email . ' - ' . $user->name);
                }
                Log::channel('site')->info('Report Mail Entry Ends To Pay was send');
            }
        }
    }
}
