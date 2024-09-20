<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity\EntryData;

readonly class Data
{
    public function __construct(
        public ?Entry $Entry,
    ) {
    }
}
