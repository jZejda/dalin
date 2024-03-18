<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class SourceType
{
    public function __construct(
        public string|null $ID,
        public string|null $NameCZ,
        public string|null $NameEN,
    ) {
    }
}
