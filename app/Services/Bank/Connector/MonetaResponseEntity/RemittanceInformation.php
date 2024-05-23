<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class RemittanceInformation
{
    public function __construct(
        public readonly Structured $structured,
    ) {
    }
}
