<?php

namespace App\View\Components\SportEvent;

use App\Enums\SportEventType;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventIcon extends Component
{
    /**
     * Create a new component instance.
     */
    public SportEventType $eventType;

    public int $sportId;

    public function __construct(SportEventType $eventType, int $sportId)
    {
        $this->eventType = $eventType;
        $this->sportId = $sportId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sport-event.event-icon');
    }
}
