<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Register extends Component
{
    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
