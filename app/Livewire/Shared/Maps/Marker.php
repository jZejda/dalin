<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Services\Map\MapMarkerVisual;
use Illuminate\Support\Carbon;

final readonly class Marker
{
    public function __construct(
        public float $lat,
        public float $lng,
        public MapMarkerVisual $visual,
        public string $label,
        public ?string $secondaryLabel,
        public ?Carbon $date,
        public ?array $region,
        public ?int $eventId,
        public ?int $orisId,
    ) {
    }
}
