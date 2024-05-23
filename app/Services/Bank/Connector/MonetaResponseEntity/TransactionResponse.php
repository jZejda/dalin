<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class TransactionResponse
{
    public function __construct(
        public int $pageCount,
        public int $pageNumber,
        public int $pageSize,
        /** @var Transactions[] $transactions */
        public array $transactions,
    ) {
    }
}
