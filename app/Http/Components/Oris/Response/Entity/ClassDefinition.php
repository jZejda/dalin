<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class ClassDefinition
{
    public function __construct(
        public string $ID,
        public string $AgeFrom,
        public string $AgeTo,
        public string $Gender,
        public string $Name,
    ) {
    }
}
