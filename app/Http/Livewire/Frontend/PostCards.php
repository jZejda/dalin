<?php

namespace App\Http\Livewire\Frontend;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        return DB::table('posts')
            ->where('private', '=', 0)
            ->limit(6)
            ->orderBy('created_at', 'desc')
            ->get();
    }

}
