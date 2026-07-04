<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

readonly class OrisServiceEntryResponse
{
    public function __construct(
        public string $status,
        public ?int $serviceEntryId = null,
    ) {
    }

    public function isOk(): bool
    {
        return $this->status === 'OK';
    }
}
