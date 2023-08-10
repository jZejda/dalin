<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use DB;
use App\Services\OrisApiService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

final class UpdateEvent implements CommonCronJobs
{
    public function run(): void
    {
        $sportEvents = DB::table('sport_events')
            ->where('last_update', '<', Carbon::now()->subDays(5))
            ->orWhereNull('last_update')
            ->whereNotNull('oris_id')
            ->where('date', '>', Carbon::now()->addDays(4))
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        foreach ($sportEvents as $sportEvent) {
            //var_dump($sportEvent->id);
            $service = new OrisApiService();
            $service->updateEvent($sportEvent->oris_id, true);
            Log::channel('site')->info('Update Event ID: ' . $sportEvent->id . ' name: ' . $sportEvent->name);
        }
    }
}
