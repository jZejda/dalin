<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class RelatedParties
{
    public function __construct(
        public ?Debtor $debtor,
        public ?DebtorAccount $debtorAccount,
    ) {
    }
}
