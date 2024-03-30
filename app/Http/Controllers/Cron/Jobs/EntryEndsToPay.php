<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use Illuminate\Support\Facades\DB;
use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EntryEndsToPay implements CommonCronJobs
{
    public function run(): void
    {
        $deadlines = [1, 2, 3];

        foreach ($deadlines as $deadline) {

            $sportEvents = DB::table('sport_events')
                ->whereNotNull('entry_date_' . $deadline)
                ->whereExists(function (Builder $query) {
                    $query->select(DB::raw(1))
                        ->from('user_entries')
                        ->whereColumn('user_entries.sport_event_id', 'sport_events.id')
                        ->where('user_entries.entry_status', '!=', EntryStatus::Cancel->value);
                })
                ->where('entry_date_' . $deadline, '<=', Carbon::now()->endOfHour()->format(AppHelper::MYSQL_DATE_TIME))
                ->where('entry_date_' . $deadline, '>=', Carbon::now()->startOfHour()->format(AppHelper::MYSQL_DATE_TIME))
                ->get();

            if (count($sportEvents) >= 1) {
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
