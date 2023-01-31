<?php

namespace App\Http\Livewire\Frontend;

use App\Models\Page;
use Livewire\Component;
use Illuminate\View\View;

class ShowPage extends Component
{
    public Page $page;

    public function mount(string $slug): void
    {
        $this->page = Page::where('slug', '=', $slug)->where('status', '=', Page::STATUS_OPEN)->first();

    }

    public function render(): View
    {
        return view('livewire.frontend.show-page', ['page' => $this->page]);
    }
}
