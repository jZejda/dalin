<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SportEventExport;
use App\Services\IofExportsService;
use Illuminate\Http\Response;
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
        $xmlUrl = null;
        $sportEventExport = SportEventExport::where('slug', '=', $slug)
            ->where('export_type', '=', SportEventExport::ENTRY_LIST_CATEGORY)
            ->first();

        $iof = new IofExportsService();

        $resource = $iof->getResourceBySlug($slug);
        if (!is_null($resource)) {
            $xmlContent = Storage::disk('events')->get($resource);
            $xmlUrl = Storage::disk('events')->url($resource);
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
            'xmlUrl' => $xmlUrl,
            'sponsorSectionId' => 0,  // logic from model
        ]);

    }

    public function downloadWithoutVakant(string|null $slug): Response
    {
        if ($slug === null) {
            abort(404);
        }

        $iof = new IofExportsService();
        $resource = $iof->getResourceBySlug($slug);

        if (is_null($resource)) {
            abort(404);
        }

        $xmlContent = Storage::disk('events')->get($resource);
        if ($xmlContent === null) {
            abort(404);
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xmlContent);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('iof', 'http://www.orienteering.org/datastandard/3.0');
        $vakants = $xpath->query('//iof:PersonStart[iof:Person/iof:Name/iof:Family[text()="Vakant"] or iof:Organisation/iof:Name[text()="Vakant"]]');

        if ($vakants !== false) {
            foreach ($vakants as $node) {
                if ($node instanceof \DOMNode && $node->parentNode !== null) {
                    $node->parentNode->removeChild($node);
                }
            }
        }

        $filename = pathinfo($resource, PATHINFO_FILENAME) . '-bez-vakantu.xml';
        $xmlOutput = $dom->saveXML();
        if ($xmlOutput === false) {
            abort(500);
        }

        return response($xmlOutput, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
