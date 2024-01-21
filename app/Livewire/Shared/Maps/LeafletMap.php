<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventMarkerType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class LeafletMap extends Component
{
    private ?MapBuilder $mapBuilder = null;
    public SportEvent $sportEvent;

    public function __construct(?MapBuilder $mapBuilder = null)
    {
        $this->mapBuilder = $mapBuilder ?? new MapBuilder();
    }

    public function render(): View
    {

        return view('filament.shared.leaflet-map-widget', [
            'mapData' => [
                'centerMap' => $this->calculateCenterMap(),
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
        $markers = $this->sportEvent->sportEventMarkers()->get();

        if ($this->mapBuilder !== null) {
            if ($this->sportEvent->gps_lat !== null && $this->sportEvent->gps_lon !== null) {
                $this->mapBuilder->addMarker(
                    (float)$this->sportEvent->gps_lat,
                    (float)$this->sportEvent->gps_lon,
                    $this->getMarkerType($this->sportEvent->stages),
                    $this->sportEvent->name,
                    $this->sportEvent->alt_name ?? '',
                );
            }

            foreach($markers as $marker) {
                $this->mapBuilder->addMarker(
                    $marker->lat,
                    $marker->lon,
                    $marker->type ?? SportEventMarkerType::DefaultMarker,
                    $marker->label,
                    $marker->desc ?? '',
                );
            }

            return $this->mapBuilder->getMarkers();
        }

        return [];
    }

    private function getMarkerType(?int $stages): SportEventMarkerType
    {
        if ($stages !== null) {
            if ($stages > 1) {
                return SportEventMarkerType::RaceStages;
            } else {
                return SportEventMarkerType::RaceSimple;
            }
        }
        return SportEventMarkerType::RaceSimple;
    }

    public function calculateCenterMap(): array
    {
        $markers = [];
        if ($this->sportEvent->gps_lat !== null && $this->sportEvent->gps_lon !== null) {
            $markers[] = [
                'lat' => (float)$this->sportEvent->gps_lat,
                'lon' => (float)$this->sportEvent->gps_lon,
            ];
        }

        /** @var SportEventMarker[] $eventMarkers */
        $eventMarkers = $this->sportEvent->sportEventMarkers()->get();
        foreach ($eventMarkers as $eventMarker) {
            $markers[] = [
                'lat' => $eventMarker->lat,
                'lon' => $eventMarker->lon,
            ];
        }

        $total = count($markers);
        if ($total === 0) {
            return ['lat' => 0, 'lon' => 0];
        }

        $lat = 0;
        $lon = 0;

        foreach ($markers as $marker) {
            $lat += $marker['lat'];
            $lon += $marker['lon'];
        }

        return ['lat' => $lat / $total, 'lon' => $lon / $total];
    }
}