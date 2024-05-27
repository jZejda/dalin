<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class DebtorAccount
{
    public function __construct(
        public Identification $identification,
    ) {
    }
}
