<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Models\SportEvent;
use App\Services\OrisApiService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

final class SyncEventStartLists implements CommonCronJobs
{
    public function run(): void
    {
        $sportEvents = SportEvent::query()
            ->where('use_oris_for_entries', true)
            ->whereNotNull('oris_id')
            ->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(4)])
            ->whereHas('userEntry', fn ($q) => $q->whereNull('real_start'))
            ->get();

        foreach ($sportEvents as $sportEvent) {
            $service = new OrisApiService();
            $service->updateStartList($sportEvent->id);
            Log::channel('site')->info('SyncStartList event ID: ' . $sportEvent->id . ' name: ' . $sportEvent->name);
        }
    }
}
