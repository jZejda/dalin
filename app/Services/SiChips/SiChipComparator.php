<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Enums\SiSyncStatus;
use App\Models\UserRaceProfile;

class SiChipComparator
{
    /**
     * Chips of a race profile: the primary one first, then the additional ones.
     *
     * @return list<SiChipData>
     */
    public function dalinChips(UserRaceProfile $profile): array
    {
        $chips = [];

        if ($profile->si !== null && $profile->si > 0) {
            $chips[] = new SiChipData($profile->si, $profile->si_sport, $profile->si_type);
        }

        foreach ($profile->chips as $chip) {
            $chips[] = new SiChipData($chip->si, $chip->sport, $chip->type);
        }

        return $chips;
    }

    /**
     * @param  array<int, OrisClubMember>|null  $orisMembers  keyed by ORIS user id, null when ORIS could not be read
     */
    public function compare(UserRaceProfile $profile, ?array $orisMembers): SiChipComparison
    {
        $dalin = $this->dalinChips($profile);

        if ($orisMembers === null) {
            return new SiChipComparison(SiSyncStatus::Unknown, $dalin);
        }

        $member = $this->findMember($profile, $orisMembers);

        if (! $member instanceof OrisClubMember) {
            return new SiChipComparison(SiSyncStatus::NotLinked, $dalin);
        }

        $oris = $member->chips;

        if ($this->keys($dalin) === $this->keys($oris)) {
            return new SiChipComparison(SiSyncStatus::InSync, $dalin, $oris, $member);
        }

        $status = match (true) {
            $oris === [] => SiSyncStatus::MissingInOris,
            $dalin === [] => SiSyncStatus::MissingInDalin,
            default => SiSyncStatus::Differs,
        };

        return new SiChipComparison($status, $dalin, $oris, $member);
    }

    /**
     * @param  array<int, OrisClubMember>  $orisMembers
     */
    private function findMember(UserRaceProfile $profile, array $orisMembers): ?OrisClubMember
    {
        if ($profile->oris_id !== null && isset($orisMembers[(int) $profile->oris_id])) {
            return $orisMembers[(int) $profile->oris_id];
        }

        // Profiles without a stored ORIS id can still be matched by the registration number.
        foreach ($orisMembers as $member) {
            if ($member->regNum !== '' && $member->regNum === $profile->reg_number) {
                return $member;
            }
        }

        return null;
    }

    /**
     * Order does not matter - ORIS slots are not meaningful, only the set of chips is.
     *
     * @param  list<SiChipData>  $chips
     * @return list<string>
     */
    private function keys(array $chips): array
    {
        $keys = array_map(fn (SiChipData $chip): string => $chip->key(), $chips);
        sort($keys);

        return $keys;
    }
}
