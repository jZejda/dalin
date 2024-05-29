<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class Identification
{
    public function __construct(
        public ?Other $other,
    ) {
    }
}
