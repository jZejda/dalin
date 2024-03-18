<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Clubs
{
    public function __construct(
        public string $ID,
        public string $Name,
        public string $Abbr,
        public string $Region,
        public string $Number
    ) {
    }
}
