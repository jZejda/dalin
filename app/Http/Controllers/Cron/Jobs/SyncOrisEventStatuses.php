<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Services\SportEvents\OrisEventStatusRefresher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Refreshes the stored ORIS status (entry-fee settlement, start list / results) of the races the club has entries in.
 */
final class SyncOrisEventStatuses implements CommonCronJobs
{
    /** Races this many days back still get their payment and results checked. */
    public const int DAYS_BACK = 14;

    /** Races further ahead than this are not interesting yet (entries still open, nothing published). */
    public const int DAYS_AHEAD = 45;

    public function run(): void
    {
        $sportEvents = SportEvent::query()
            ->with('orisStatus')
            ->whereNotNull('oris_id')
            ->whereBetween('date', [Carbon::today()->subDays(self::DAYS_BACK), Carbon::today()->addDays(self::DAYS_AHEAD)])
            ->whereHas('userEntry', static fn (Builder $query): Builder => $query->whereIn('entry_status', EntryStatus::activeValues()))
            // Settled races — paid and with results — will not change any more
            ->whereDoesntHave('orisStatus', static fn (Builder $query): Builder => $query
                ->where('results_count', '>', 0)
                ->where('to_be_paid', '<=', 0))
            ->orderBy('date')
            ->get();

        $refresher = new OrisEventStatusRefresher();
        foreach ($sportEvents as $sportEvent) {
            if ($refresher->refreshQuietly($sportEvent) !== null) {
                Log::channel('site')->info('SyncOrisEventStatus event ID: '.$sportEvent->id.' name: '.$sportEvent->name);
            }
        }
    }
}
