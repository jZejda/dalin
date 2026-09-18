<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Livewire\Component;

class EventList extends Component
{
    public bool $terrain = false;

    public function render(): View|Factory|Application
    {
        return view(
            $this->terrain ? 'livewire.frontend.terrain.event-list' : 'livewire.frontend.event-list',
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
            ->where('cancelled', '=', 0)
            ->whereIn('event_type', [
                SportEventType::Race->value,
                SportEventType::Training->value,
                SportEventType::TrainingCamp->value,
            ])
            ->sport(1)
            ->limit($this->terrain ? 3 : 6)
            ->orderBy('date', 'asc')
            ->get();
    }
}
