<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Models\UserRaceProfile;
use App\Models\UserRaceProfileChip;
use Illuminate\Support\Facades\DB;
use Throwable;

class SiChipImporter
{
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
