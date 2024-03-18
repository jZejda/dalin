<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Services
{
    public function __construct(
        public string $ID,
        public string $NameCZ,
        public ?string $NameEN,
        public ?string $LastBookingDateTime,
        public ?string $UnitPrice,
        public ?string $QtyAvailable,
        public ?int $QtyRemaining,
    ) {
    }
}
