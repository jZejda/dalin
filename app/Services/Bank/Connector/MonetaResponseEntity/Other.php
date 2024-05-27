<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class Other
{
    public function __construct(
        public string $identification,
    ) {
    }
}
