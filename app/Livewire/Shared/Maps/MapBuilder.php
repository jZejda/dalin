<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventMarkerType;
use Illuminate\Support\Carbon;

final class MapBuilder
{
    /** @var Marker[] */
    private array $markers = [];

    public function addMarker(
        float $lat,
        float $lng,
        SportEventMarkerType $markerType,
        string $label,
        ?string $secondaryLabel,
        ?Carbon $date,
        ?array $region,
        ?int $eventId,
        ?int $orisId,
    ): void {
        $this->markers[] = new Marker(
            $lat,
            $lng,
            $markerType,
            $label,
            $secondaryLabel,
            $date,
            $region,
            $eventId,
            $orisId,
        );
    }

    /**
     * @return Marker[] array
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }
}
