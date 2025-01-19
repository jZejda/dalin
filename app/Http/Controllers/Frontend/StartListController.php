<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SportEventExport;
use App\Services\IofExportsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StartListController extends Controller
{
    public function singleStartList(string|null $slug): View
    {
        if ($slug === null) {
            abort('404');
        }

        $xmlContent = null;
        $sportEventExport = SportEventExport::where('slug', '=', $slug)
            ->where('export_type', '=', SportEventExport::ENTRY_LIST_CATEGORY)
            ->first();

        $iof = new IofExportsService();

        $resource = $iof->getResourceBySlug($slug);
        if (!is_null($resource)) {
            $xmlContent = Storage::disk('events')->get($resource);
        }

        $eventName = null;
        $eventAttributes = null;
        $classStart = null;


        if (!is_null($xmlContent)) {
            $startList = $iof->getStartList($xmlContent);
            $eventName = $startList->getEvent()->getName();

            $startListAttributes = $iof->getStartListAttributes($xmlContent);
            $eventAttributes[] = $startListAttributes;

            $classStart = $startList->getClassStart();
        }

        return view('pages.frontend.single-start-list', [
            'eventName' => $eventName,
            'eventAttributes' => $eventAttributes,
            'classStart' => $classStart,
            'sportEventExport' => $sportEventExport,
            'sponsorSectionId' => 0,  // logic from model
        ]);

    }
}
