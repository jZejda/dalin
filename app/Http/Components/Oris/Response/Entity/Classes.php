<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Classes
{
    public function __construct(
        public string $ID,
        public string $Name,
        public string $Distance,
        public string $Climbing,
        public string $Controls,
        public ClassDefinition $ClassDefinition,
        public string $Fee,
    ) {
    }
}
