<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class TransactionList
{
    public function __construct(
        /** @var Transaction[] $transaction */
        public array $transaction,
    ) {
    }
}
