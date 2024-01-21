<?php

declare(strict_types=1);

namespace App\Livewire\Backend\Widgets;

use App\Models\SportEvent;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class EventMap extends Widget
{
    protected int|string|array $columnSpan = 1;

    public SportEvent $model;

    public function render(): View
    {
        return view('livewire.filament.backend.widgets.event-map', ['markers' => $this->getMarkers()]);
    }

    private function getMarkers(): array
    {
        return  [
            [
                'position' => [
                    'lat' => 12.625485,
                    'lng' => 79.821091
                ],
                'marker' => 'obRaceSimple',
                'draggable' => true
            ],
            [
                'position' => [
                    'lat' => 18.625293,
                    'lng' => 79.817926
                ],
                'marker' => 'obRaceDot',
                'draggable' => false
            ],
            [
                'position' => [
                    'lat' => 45.625182,
                    'lng' => 50.81464
                ],
                'marker' => 'trainingDot',
                'draggable' => true
            ]
        ];
    }
}
