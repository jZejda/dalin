<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class Transactions
{
    public function __construct(
        public Amount $amount,
        public string $creditDebitIndicator,
        public EntryDetails $entryDetails,
        public string $entryReference,
        public string $status,
    ) {
    }
}
