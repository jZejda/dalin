<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SportEventExport;
use App\Services\IofExportsService;
use App\Services\Seo\EventListSeo;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ResultListController extends Controller
{
    public function singleResultList(string|null $slug, EventListSeo $eventListSeo): Response
    {
        if ($slug === null) {
            abort(404);
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

        // The view shows its own "not found" message; the status keeps search engines from
        // indexing the empty page as a soft 404
        return response()->view('pages.frontend.single-result-list', [
            'eventName' => $eventName ?? null,
            'eventAttributes' => $eventAttributes ?? null,
            'classResult' => $classResult ?? null,
            'sportEventExport' => $sportEventExport,
            'seo' => $eventListSeo->resultList($sportEventExport, $eventName ?? null, $classResult ?? null),
            'sponsorSectionId' => 0,  // logic from model
        ], isset($classResult) ? 200 : 404);

    }
}
