<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Enums\SiSyncStatus;
use App\Models\UserRaceProfile;

/**
 * Dalin -> ORIS: make the ORIS chips of a race profile match the Dalin ones.
 */
class SiChipPusher
{
    public function __construct(
        private readonly OrisSiClient $client,
        private readonly SiChipComparator $comparator,
    ) {
    }

    /**
     * Comparison used by the UI (club list may come from cache).
     *
     * @throws OrisSiException
     */
    public function compare(UserRaceProfile $profile): SiChipComparison
    {
        return $this->comparator->compare($profile, $this->client->clubMembers());
    }

    /**
     * Parameters that would be sent to ORIS, computed from fresh ORIS data. Nothing is sent.
     *
     * @return array<string, string|int>
     *
     * @throws OrisSiException
     */
    public function preview(UserRaceProfile $profile): array
    {
        $comparison = $this->freshPushableComparison($profile);

        /** @var OrisClubMember $member */
        $member = $comparison->orisMember;

        return $this->client->buildEditPayload($member, $comparison->dalinChips);
    }

    /**
     * Sends the Dalin chips to ORIS. Reads ORIS fresh first, because the whole person
     * record is written back and a stale copy would overwrite newer changes.
     *
     * @throws OrisSiException
     */
    public function push(UserRaceProfile $profile): void
    {
        $comparison = $this->freshPushableComparison($profile);

        if ($comparison->status === SiSyncStatus::InSync) {
            return;
        }

        /** @var OrisClubMember $member */
        $member = $comparison->orisMember;

        $this->client->editChips($member, $comparison->dalinChips);
    }

    /**
     * @throws OrisSiException
     */
    private function freshPushableComparison(UserRaceProfile $profile): SiChipComparison
    {
        if (! $this->client->isConfigured()) {
            throw new OrisSiException(__('si-chip.errors.no_club_key'));
        }

        $comparison = $this->comparator->compare($profile, $this->client->clubMembers(true));

        if (! $comparison->orisMember instanceof OrisClubMember) {
            throw new OrisSiException(__('si-chip.errors.not_linked'));
        }

        if ($comparison->dalinChips === []) {
            // Never wipe the ORIS chips because Dalin has none - that is a pull, not a push.
            throw new OrisSiException(__('si-chip.errors.nothing_to_push'));
        }

        return $comparison;
    }
}
