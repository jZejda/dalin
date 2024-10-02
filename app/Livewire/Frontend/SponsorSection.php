<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Livewire\Component;

class SponsorSection extends Component
{
    public ?int $sponsorId = null;

    public function render(): Factory|View|Application
    {
        return view('livewire.frontend.sponsor-section', ['id' => $this->sponsorId]);
    }
}
