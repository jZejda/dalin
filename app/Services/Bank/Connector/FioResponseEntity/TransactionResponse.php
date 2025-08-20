<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class TransactionResponse
{
    public function __construct(
        public AccountStatement $accountStatement,
    ) {
    }
}
