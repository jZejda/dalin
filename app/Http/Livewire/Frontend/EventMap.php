<?php

declare(strict_types=1);

namespace App\Http\Livewire\Frontend;

use Illuminate\View\View;
use Livewire\Component;

class EventMap extends Component
{
    public function render(): View
    {
        $initialMarkers = [
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


        return view('livewire.frontend.event-map', ['initialMarkers' => $initialMarkers]);
    }
}
