<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class Amount
{
    public function __construct(
        public string $currency,
        public int $value,
    ) {
    }
}
