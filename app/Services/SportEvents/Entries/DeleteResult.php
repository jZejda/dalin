<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

readonly class DeleteResult
{
    public function __construct(
        public bool $success,
        public bool $wasOrisEntry,
        public ?int $orisEventId = null,
        /** Non-OK ORIS status string when ORIS rejected the delete. */
        public ?string $orisStatusError = null,
    ) {
    }
}
