<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventMarkerType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;

final class BaseMap
{
    private ?MapBuilder $mapBuilder;

    public function __construct(?MapBuilder $mapBuilder = null)
    {
        $this->mapBuilder = $mapBuilder ?? new MapBuilder();
    }

    /**
     * @return Marker[]
     */
    public function getMarkersFromEvent(SportEvent $sportEvent): array
    {
        /** @var SportEventMarker[] $markers */
        $markers = $sportEvent->sportEventMarkers()->get();

        if ($this->mapBuilder !== null) {
            if ($sportEvent->gps_lat !== null && $sportEvent->gps_lon !== null) {
                $this->mapBuilder->addMarker(
                    (float)$sportEvent->gps_lat,
                    (float)$sportEvent->gps_lon,
                    $this->getMarkerType($sportEvent->stages),
                    $sportEvent->name,
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

    public function calculateCenterMapFromEvent(SportEvent $sportEvent): array
    {
        $markers = [];
        if ($sportEvent->gps_lat !== null && $sportEvent->gps_lon !== null) {
            $markers[] = [
                'lat' => (float)$sportEvent->gps_lat,
                'lon' => (float)$sportEvent->gps_lon,
            ];
        }

        /** @var SportEventMarker[] $eventMarkers */
        $eventMarkers = $sportEvent->sportEventMarkers()->get();
        foreach ($eventMarkers as $eventMarker) {
            $markers[] = [
                'lat' => $eventMarker->lat,
                'lon' => $eventMarker->lon,
            ];
        }

        return $this->getCenterMapCoords($markers);
    }

    private function getCenterMapCoords(array $markers): array
    {
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
