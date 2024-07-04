<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class ValueDate
{
    public function __construct(
        public string $date,
    ) {
    }
}
