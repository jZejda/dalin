<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsersEntryExport;
use App\Models\SportEvent;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserEntryController
{
    public function export(int $eventId): Response|BinaryFileResponse
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = SportEvent::where('id', '=', $eventId)->first();

        return (new UsersEntryExport())->forEventEntryId($eventId, $sportEvent)
            ->download('prihlasky-' . Str::slug($sportEvent->name) . '-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }
}
