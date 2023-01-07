<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class HelloWorld extends Component
{
    public string $name = 'Jirkssa';
    public bool $loud = false;
    public array $greeting = ['ahoj'];

    public function resetName(): void
    {
        $this->name = 'Pepa';
    }

    public function render(): ?View
    {
        return view('livewire.hello-world');
    }
}
