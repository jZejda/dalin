<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Locations
{
    public function __construct(
        public string $ID,
        public string $GPSLat,
        public string $GPSLon,
        public ?string $NameCZ,
        public ?string $NameEN,
        public ?string $Letter,
    ) {
    }
}
