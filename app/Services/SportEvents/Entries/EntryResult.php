<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Models\SportClass;

readonly class EntryResult
{
    public function __construct(
        public bool $success,
        public ?UserEntry $entry = null,
        public ?UserRaceProfile $userRaceProfile = null,
        public ?SportClass $sportClass = null,
        /** Non-OK ORIS status string when ORIS rejected the entry. */
        public ?string $orisStatusError = null,
        /** sport_event.oris_id — used to build ORIS URL in success notification. */
        public ?int $orisEventId = null,
    ) {
    }
}
