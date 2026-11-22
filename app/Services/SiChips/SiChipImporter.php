<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Models\UserRaceProfile;
use App\Models\UserRaceProfileChip;
use Illuminate\Support\Facades\DB;
use Throwable;

class SiChipImporter
{
    public function __construct(
        private readonly ?OrisSiClient $client = null,
        private readonly ?SiChipComparator $comparator = null,
    ) {
    }

    /**
     * Same as pullFromOris(), but reads ORIS fresh first - the cached list can be minutes old
     * and would overwrite Dalin with outdated chips.
     *
     * @throws OrisSiException
     * @throws Throwable
     */
    public function pullFresh(UserRaceProfile $profile): void
    {
        $client = $this->client ?? app(OrisSiClient::class);
        $comparison = ($this->comparator ?? new SiChipComparator())->compare($profile, $client->clubMembers(true));

        if (! $comparison->orisMember instanceof OrisClubMember) {
            throw new OrisSiException(__('si-chip.errors.not_linked'));
        }

        $this->pullFromOris($profile, $comparison->orisMember);
    }

    /**
     * Overwrite the Dalin chips of a profile with the ORIS ones: the first ORIS chip becomes
     * the primary chip, the others the additional chips.
     *
     * @throws Throwable
     */
    public function pullFromOris(UserRaceProfile $profile, OrisClubMember $member): void
    {
        $chips = $member->chips;

        if ($chips === []) {
            // Never wipe Dalin chips because ORIS is empty - that is a push to ORIS, not a pull.
            return;
        }

        DB::transaction(function () use ($profile, $chips): void {
            $primary = array_shift($chips);

            $profile->update([
                'si' => $primary->si,
                'si_sport' => $primary->sport,
                'si_type' => $primary->type,
            ]);

            $profile->chips()->delete();

            foreach ($chips as $index => $chip) {
                UserRaceProfileChip::query()->create([
                    'user_race_profile_id' => $profile->id,
                    'si' => $chip->si,
                    'sport' => $chip->sport,
                    'type' => $chip->type,
                    'sort_order' => $index + 1,
                ]);
            }
        });

        $profile->unsetRelation('chips');
    }
}
