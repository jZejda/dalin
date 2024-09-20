<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventMarkerType;
use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

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
                    (float) $sportEvent->gps_lat,
                    (float) $sportEvent->gps_lon,
                    $this->getMarkerType($sportEvent),
                    $sportEvent->name,
                    $sportEvent->alt_name ?? '',
                    $sportEvent->date,
                    $sportEvent->region,
                    $sportEvent->id,
                    $sportEvent->oris_id,
                );
            }

            foreach ($markers as $marker) {
                $this->mapBuilder->addMarker(
                    $marker->lat,
                    $marker->lon,
                    $marker->type ?? SportEventMarkerType::DefaultMarker,
                    $marker->label,
                    $marker->desc ?? '',
                    $marker->date,
                    $marker->region,
                    $sportEvent->id,
                    $sportEvent->oris_id,
                );
            }

            return $this->mapBuilder?->getMarkers();
        }

        return [];
    }

    /**
     * @return Marker[]
     */
    public function getMarkersFromEvents(Collection|array $sportEvents): array
    {
        /** @var SportEvent $sportEvent */
        foreach ($sportEvents as $sportEvent) {
            if ($this->mapBuilder !== null) {
                if ($sportEvent->gps_lat !== null && $sportEvent->gps_lon !== null) {
                    $this->mapBuilder->addMarker(
                        lat: (float) $sportEvent->gps_lat,
                        lng: (float) $sportEvent->gps_lon,
                        markerType: $this->getMarkerType($sportEvent),
                        label: $sportEvent->name,
                        //                        popupContent: new HtmlString('<span class="text-sm text-yellow-500 dark:text-yellow-400">' . $sportEvent->alt_name . '</span>') ?? '',
                        secondaryLabel: $sportEvent->alt_name ?? '',
                        date: $sportEvent->date,
                        region: $sportEvent->region,
                        eventId: $sportEvent->id,
                        orisId: $sportEvent->oris_id,
                    );
                }
            }
        }

        return $this->mapBuilder?->getMarkers() ?? [];
    }

    private function getMarkerType(SportEvent $sportEvent): SportEventMarkerType
    {

        if ($sportEvent->event_type === SportEventType::Training) {
            return SportEventMarkerType::Training;
        }

        if ($sportEvent->event_type === SportEventType::TrainingCamp) {
            return SportEventMarkerType::TrainingCamp;
        }

        if ($sportEvent->stages !== null) {
            if ($sportEvent->stages > 1) {
                return SportEventMarkerType::ObRaceStages;
            }
        }

        return SportEventMarkerType::ObRaceSimple;
    }

    public function calculateCenterMapFromEvent(SportEvent $sportEvent): array
    {
        $markers = [];
        if ($sportEvent->gps_lat !== null && $sportEvent->gps_lon !== null) {
            $markers[] = [
                'lat' => (float) $sportEvent->gps_lat,
                'lon' => (float) $sportEvent->gps_lon,
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

    /**
     * @param  Marker[]  $markers
     */
    public function getCenterMapCoordsFromMarkers(array $markers): array
    {
        $total = count($markers);
        if ($total === 0) {
            return ['lat' => 0, 'lon' => 0];
        }

        $lat = 0;
        $lon = 0;

        foreach ($markers as $marker) {
            $lat += $marker->lat;
            $lon += $marker->lng;
        }

        return ['lat' => $lat / $total, 'lon' => $lon / $total];
    }

    /**
     * Calculate the center coordinates from an array or collection of SportEvent models.
     *
     * @param  SportEvent[]  $sportEvents
     */
    public function calculateCenterFromSportEvents(Collection|array $sportEvents): array
    {
        $markers = [];

        /** @var SportEvent $sportEvent */
        foreach ($sportEvents as $sportEvent) {
            if ($this->isValidCoordinate($sportEvent->gps_lat) && $this->isValidCoordinate($sportEvent->gps_lon)) {
                $markers[] = [
                    'lat' => (float) $sportEvent->gps_lat,
                    'lon' => (float) $sportEvent->gps_lon,
                ];
            }
        }

        return $this->getCenterMapCoords($markers);
    }

    /**
     * Check if a coordinate is valid (not null and not zero).
     */
    private function isValidCoordinate(string|null $coordinate): bool
    {
        return $coordinate !== null && $coordinate !== '0';
    }
}
