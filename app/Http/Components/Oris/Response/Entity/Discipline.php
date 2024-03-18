<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Discipline
{
    public function __construct(
        public string $ID,
        public string $ShortName,
        public string $NameCZ,
        public string $NameEN,
    ) {
    }
}
