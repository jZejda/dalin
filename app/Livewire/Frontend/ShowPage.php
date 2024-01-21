<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\SportEventExport;
use App\Shared\Entities\FrontendLinks;
use App\Shared\Helpers\EmptyType;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\View\View;

class ShowPage extends Component
{
    public Page|null $page = null;

    public function mount(string|null $slug): void
    {
        if (EmptyType::stringNotEmpty($slug)) {
            $this->page = Page::where('slug', '=', $slug)->where('status', '=', Page::STATUS_OPEN)->first();
        }
    }

    /**
     * @throws Exception
     */
    public function render(): View
    {
        if ($this->page !== null) {
            if ($this->page->page_menu) {
                return view('livewire.frontend.show-page-aside', [
                    'page' => $this->page,
                    'relatedPages' => $this->getAsideMenu($this->page),
                    'relatedLinks' => $this->getAssideLinks($this->page),
                ]);
            } else {
                return view('', ['page' => $this->page]);
            }
        } else {
            abort(404);
        }
    }

    /**
     * @return FrontendLinks[]
     * @throws Exception
     */
    private function getAssideLinks(Page $page): array
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

                $name = match($link->export_type) {
                    SportEventExport::ENTRY_LIST_CATEGORY => 'Startovka',
                    SportEventExport::RESULT_LIST_CATEGORY => 'Vysledky',
                    default => throw new Exception('Unsupported'),
                };

                $routeUri = match($link->export_type) {
                    SportEventExport::ENTRY_LIST_CATEGORY => 'startovka',
                    SportEventExport::RESULT_LIST_CATEGORY => 'vysledky',
                    default => throw new Exception('Unsupported'),
                };

                $exportLinks[] = new FrontendLinks(
                    type: $link->export_type,
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
