<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class AccountStatement
{
    public function __construct(
        public Info $info,
        public TransactionList $transactionList
    ) {
    }
}
