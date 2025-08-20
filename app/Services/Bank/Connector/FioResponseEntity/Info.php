<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class Info
{
    public function __construct(
        public string $accountId,
        public string $bankId,
        public string $currency,
        public string $iban,
        public string $bic,
        public float $openingBalance,
        public float $closingBalance,
        public string $dateStart,
        public string $dateEnd,
        public ?string $yearList,
        public ?string $idList,
        public int $idFrom,
        public int $idTo,
        public ?int $idLastDownload
    ) {
    }
}
