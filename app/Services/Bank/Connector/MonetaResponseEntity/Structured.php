<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\MonetaResponseEntity;

readonly class Structured
{
    public function __construct(
        public CreditorReferenceInformation $creditorReferenceInformation,
    ) {
    }
}
