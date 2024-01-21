<?php

namespace App\Livewire\Frontend;

use Illuminate\View\View;
use Livewire\Component;

class NewPost extends Component
{
    public function render(): View
    {
        return view('livewire.frontend.new-post');
    }
}
