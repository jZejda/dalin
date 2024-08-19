<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector;

use App\Services\Bank\Enums\TransactionIndicator;
use Carbon\Carbon;

readonly class Transaction
{
    public function __construct(
        public string $externalKey,
        public TransactionIndicator $transactionIndicator,
        public Carbon $dateTime,
        public float $amount,
        public string $currency,
        public ?string $bankAccountIdentifier,
        public ?string $variableSymbol,
        public ?string $specificSymbol,
        public ?string $constantSymbol,
        public ?string $note,
        public ?string $description,
        public ?string $error,
        public ?string $status,
    ) {
    }
}
