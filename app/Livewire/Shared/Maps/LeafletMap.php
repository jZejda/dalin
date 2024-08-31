<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Models\SportEvent;
use App\Models\SportEventMarker;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class LeafletMap extends Component
{
    private ?BaseMap $baseMap = null;

    public SportEvent $sportEvent;

    public function __construct(?MapBuilder $mapBuilder = null, ?BaseMap $baseMap = null)
    {
        $this->baseMap = $mapBuilder ?? new BaseMap;
    }

    public function render(): View
    {
        return view('filament.shared.leaflet-map-widget', [
            'mapData' => [
                'centerMap' => $this->baseMap->calculateCenterMapFromEvent($this->sportEvent),
                'markers' => $this->getMapDataFromEvent(),
            ],
        ]);
    }

    /**
     * @return Marker[]
     */
    public function getMapDataFromEvent(): array
    {
        /** @var SportEventMarker[] $markers */
        return $this->baseMap->getMarkersFromEvent($this->sportEvent);
    }
}
