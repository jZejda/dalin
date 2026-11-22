<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Enums\SiSyncStatus;

final readonly class SiChipComparison
{
    /**
     * @param  list<SiChipData>  $dalinChips
     * @param  list<SiChipData>  $orisChips
     */
    public function __construct(
        public SiSyncStatus $status,
        public array $dalinChips = [],
        public array $orisChips = [],
        public ?OrisClubMember $orisMember = null,
    ) {
    }

    /**
     * Pulling ORIS chips into Dalin is only meaningful when ORIS actually has some.
     */
    public function canPullFromOris(): bool
    {
        return $this->orisChips !== [] && in_array($this->status, [SiSyncStatus::Differs, SiSyncStatus::MissingInDalin], true);
    }

    /**
     * Pushing Dalin chips to ORIS is only meaningful when Dalin actually has some.
     */
    public function canPushToOris(): bool
    {
        return $this->dalinChips !== [] && in_array($this->status, [SiSyncStatus::Differs, SiSyncStatus::MissingInOris], true);
    }
}
