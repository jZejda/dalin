<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

readonly class UpdateResult
{
    public function __construct(
        public bool $success,
        public bool $wasOrisEntry,
        /** sport_event.oris_id — used to build ORIS URL in success notification. */
        public ?int $orisEventId = null,
        /** Non-OK ORIS status string when ORIS rejected the update. */
        public ?string $orisStatusError = null,
    ) {
    }
}
