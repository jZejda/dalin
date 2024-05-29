<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class EntryDetails
{
    public function __construct(
        public TransactionDetails $transactionDetails,
    ) {
    }
}
