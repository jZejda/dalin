<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\SportEvent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Livewire\Component;

class EventList extends Component
{
    public function render(): View|Factory|Application
    {
        return view(
            'livewire.frontend.event-list',
            ['events' => $this->getSportEvents()]
        );
    }

    /**
     * @return Collection|SportEvent[]
     */
    private function getSportEvents(): Collection
    {
        return SportEvent::query()
            ->where('date', '>', Carbon::now()->subDays(2))
            ->whereIn('event_type', ['race', 'training', 'trainingCamp'])
            ->sport(1)
            ->limit(6)
            ->orderBy('date', 'asc')
            ->get();
    }
}