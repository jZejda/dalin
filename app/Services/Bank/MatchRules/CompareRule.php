<?php

declare(strict_types=1);

namespace App\Services\Bank\MatchRules;

use App\Services\Bank\Enums\CompareType;
use App\Services\Bank\Enums\TransactionIndicator;

final readonly class CompareRule
{
    public function __construct(
        public TransactionIndicator $transactionIndicator,
        public CompareType $compareType,
        public null|string $variablePrefix,
    ) {
    }
}
