<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class TransactionDetails
{
    public function __construct(
        public RemittanceInformation $remittanceInformation,
        public References $references,
    ) {
    }
}
