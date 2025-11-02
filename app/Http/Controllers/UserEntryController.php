<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsersEntryExport;
use App\Models\SportEvent;
use App\Services\IofExportsService;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserEntryController
{
    public function exportXlsx(int $eventId): Response|BinaryFileResponse
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = SportEvent::where('id', '=', $eventId)->first();

        return (new UsersEntryExport())->forEventEntryId($eventId, $sportEvent)
            ->download('Excel-Prihlasky-' . Str::slug($sportEvent->name) . '-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function exportEntryListIofV3(int $eventId): Response|BinaryFileResponse
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = SportEvent::where('id', '=', $eventId)->first();

        if ($sportEvent === null) {
            abort(404, 'Sport event not found');
        }

        $iofExportsService = new IofExportsService();
        $xmlContent = $iofExportsService->generateEntryListXml($sportEvent);

        $filename = 'IOF-V3-Prihlasky-' . Str::slug($sportEvent->name) . '-' . Carbon::now()->format('Y-m-d') . '.xml';


        //dd($filename);

        return response($xmlContent)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
