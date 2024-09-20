<?php

declare(strict_types=1);

namespace App\Services\Bank\Entity;

use App\Http\Components\Oris\Response\Entity\EntryData\Data;

readonly class Transactions
{
    public function __construct(
        public string $Method,
        public string $Format,
        public string $Status,
        public string $ExportCreated, //YMD HIS
        public ?Data $Data,
    ) {
    }
}
