<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class News
{
    public function __construct(
        public string $ID,
        public string $Text,
        public string $Date,
    ) {
    }
}
