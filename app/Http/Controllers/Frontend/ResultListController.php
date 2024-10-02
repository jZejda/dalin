<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SportEventExport;
use App\Services\IofExportsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResultListController extends Controller
{
    public function singleResultList(string|null $slug): View
    {
        if ($slug === null) {
            abort('404');
        }

        $xmlContent = null;
        $sportEventExport = SportEventExport::where('slug', '=', $slug)
            ->where('export_type', '=', SportEventExport::RESULT_LIST_CATEGORY)
            ->first();

        $iof = new IofExportsService();

        $resource = $iof->getResourceBySlug($slug);
        if (!is_null($resource)) {
            $xmlContent = Storage::disk('events')->get($resource);
        }


        if (!is_null($xmlContent)) {
            $resultList = $iof->getResultList($xmlContent);
            $eventName = $resultList->getEvent()->getName();

            $resultListAttributes = $iof->getStartListAttributes($xmlContent);
            $eventAttributes[] = $resultListAttributes;

            $classResult = $resultList->getClassResult();
        }

        return view('pages.frontend.single-result-list', [
            'eventName' => $eventName ?? null,
            'eventAttributes' => $eventAttributes ?? null,
            'classResult' => $classResult ?? null,
            'sportEventExport' => $sportEventExport,
            'sponsorSectionId' => 0,  // logic from model
        ]);

    }
}
