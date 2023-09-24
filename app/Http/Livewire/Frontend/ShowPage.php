<?php

declare(strict_types=1);

namespace App\Http\Livewire\Frontend;

use App\Models\Page;
use App\Shared\Helpers\EmptyType;
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

    public function render(): View
    {
        if ($this->page !== null) {
            if ($this->page->page_menu) {
                return view('livewire.frontend.show-page-aside', [
                    'page' => $this->page,
                    'relatedPages' => $this->getAsideMenu($this->page)
                ]);
            } else {
                return view('livewire.frontend.show-page', ['page' => $this->page]);
            }
        } else {
            abort(404);
        }
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
            ->orderByDesc('weight')
            ->orderByDesc('title')
            ->get();
    }
}
