<?php

declare(strict_types=1);

namespace App\Http\Livewire\Frontend;

use App\Models\Page;
use App\Shared\Helpers\EmptyType;
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
            return view('livewire.frontend.show-page', ['page' => $this->page]);
        } else {
            abort(404);
        }
    }
}
