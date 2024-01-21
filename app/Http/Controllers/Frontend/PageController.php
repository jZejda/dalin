<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Enums\SportEventExportsType;
use App\Http\Controllers\Controller;
use App\Models\ContentCategory;
use App\Models\SportEventExport;
use App\Shared\Entities\FrontendLinks;
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Page;
use Exception;

class PageController extends Controller
{
    /**
     * @throws Exception
     */
    public function page(string $slug): View
    {
        $page = null;
        if (EmptyType::stringNotEmpty($slug)) {
            $page = Page::where('slug', '=', $slug)->where('status', '=', Page::STATUS_OPEN)->first();
        }

        if ($page !== null) {
            if ($page->page_menu) {
                return view('livewire.frontend.show-page-aside', [
                    'page' => $page,
                    'relatedPages' => $this->getAsideMenu($page),
                    'relatedLinks' => $this->getAsideLinks($page),
                ]);
            } else {
                return view('pages.frontend.show-page', ['page' => $page]);
            }
        } else {
            abort(404);
        }
    }

    /**
     * @return FrontendLinks[]
     * @throws Exception
     */
    private function getAsideLinks(Page $page): array
    {

        /** @var ContentCategory|null $pageCategory */
        $pageCategory = $page->contentCategory()->first();

        $links = null;
        if (!is_null($pageCategory)) {
            $links = SportEventExport::where('sport_event_id', '=', $pageCategory->sport_event_id)->get();
        }

        $exportLinks = [];
        if (!is_null($links) && $links->isNotEmpty()) {
            /** @var SportEventExport $link */
            foreach ($links as $link) {

                /** @var SportEventExportsType $linkExportType */
                $linkExportType = $link->export_type;

                $name = $linkExportType->getAsideLinkTitle();
                $routeUri = $linkExportType->getUrlPart();

                $exportLinks[] = new FrontendLinks(
                    type: $linkExportType,
                    title: $name,
                    url: url('/' .$routeUri. '/' . $link->slug)
                );
            }
        }

        return $exportLinks;
    }

    /**
     * @param Page $page
     * @return Collection
     */
    private function getAsideMenu(Page $page): Collection
    {
        return DB::table('pages')
            ->where('content_category_id', '=', $page->content_category_id)
            ->whereIn('status', [Page::STATUS_OPEN]) //TODO if logged show redactors
            ->orderBy('weight')
            ->get();
    }
}
