<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Org
{
    public function __construct(
        public ?string $ID,
        public ?string $Abbr,
        public ?string $Name,
    ) {
    }
}
