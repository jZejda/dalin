<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class References
{
    public function __construct(
        public string $clearingSystemReference,
        public string $endToEndIdentification,
        public string $transactionDescription,
    ) {
    }
}
