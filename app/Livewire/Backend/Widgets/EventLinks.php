<?php

declare(strict_types=1);

namespace App\Livewire\Backend\Widgets;

use App\Models\SportEvent;
use App\Models\SportEventLink;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class EventLinks extends Widget
{
    protected int|string|array $columnSpan = 1;

    public SportEvent $model;

    public function render(): View
    {
        return view('livewire.filament.backend.widgets.event-links', ['sportEventLinks' => $this->getLinks()]);
        //        return <<<'HTML'
        //        <div>
        //            {{-- The whole world belongs to you. --}}
        //        </div>
        //        HTML;
    }

    private function getLinks(): Collection
    {
        return SportEventLink::query()
            ->where('sport_event_id', '=', $this->model->id)
            ->get();
    }
}
