<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventMarkerType;

final class MapBuilder
{
    /** @var Marker[] $markers */
    private array $markers = [];

    public function addMarker(
        float $lat,
        float $lng,
        SportEventMarkerType $markerType,
        string $label,
        string $popupContent,
    ): void {
        $this->markers[] = new Marker($lat, $lng, $markerType, $label, $popupContent);
    }

    /**
     * @return Marker[] array
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }

}
