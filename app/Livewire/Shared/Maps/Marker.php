<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventMarkerType;

final readonly class Marker
{
    public function __construct(
        public float $lat,
        public float $lng,
        public SportEventMarkerType $markerType,
        public string $label,
        public string $popupContent,
    ) {}
}
