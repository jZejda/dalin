<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Services\OrisApiService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class UpdateEvent implements CommonCronJobs
{
    public function run(): void
    {
        $sportEvents = DB::table('sport_events')
            ->where(function (Builder $query) {
                $query->where('last_update', '<', Carbon::now()->subDays(2))
                    ->orWhereNull('last_update');
            })
            ->where('date', '>', Carbon::now()->subDays(10))
            ->whereNotNull('oris_id')
            ->orderBy('date', 'asc')
            ->limit(15)
            ->get();

        foreach ($sportEvents as $sportEvent) {
            $service = new OrisApiService();
            $service->updateEvent($sportEvent->oris_id, true);
            Log::channel('site')->info('Update Event ID: ' . $sportEvent->id . ' name: ' . $sportEvent->name);
        }
    }
}
