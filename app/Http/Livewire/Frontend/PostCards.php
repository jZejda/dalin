<?php

namespace App\Http\Livewire\Frontend;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class PostCards extends Component
{
    public function render(): View
    {
        //dd($this->getLastPosts());

        return view('livewire.frontend.post-cards', ['posts' => $this->getLastPosts()]);
    }

    private function getLastPosts(): Collection
    {
        return Post::where('content_mode', '=', 1)
            ->limit(6)
            ->orderBy('created_at', 'desc')
            ->get();
    }

}
