<?php

namespace App\Http\Livewire\Frontend;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Navbar extends Component
{
    public function render(): View
    {
        return view('livewire.frontend.navbar');
    }
}
