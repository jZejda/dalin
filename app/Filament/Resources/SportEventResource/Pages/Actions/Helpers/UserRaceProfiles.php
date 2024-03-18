<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages\Actions\Helpers;

use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Models\UserSetting;
use App\Shared\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRaceProfiles
{
    public function getUserRaceProfiles(Model|int|null|string $model, bool $registerAnyone = false): Collection
    {
        /** @var SportEvent $model */
        $sportEvent = $model;
        $relevantUserRaceProfile = new Collection();

        if (AppHelper::allowModifyUserEntry($sportEvent)) {
            return new Collection();
        }

        if ((!is_null($sportEvent->oris_id) && $sportEvent->use_oris_for_entries)) {

            //vyselektuje relevatni profily pro uzivatel
            $relevantUserRaceProfile = $this->getRelevantRaceProfiles($registerAnyone);

            $relevantUserRaceProfile = $relevantUserRaceProfile->whereNotNull('oris_id')
                ->pluck('user_race_full_name', 'oris_id');

            // zjisti id profil; ktere jsou uz v zavode pro uzivatele
            $entries = DB::table('user_race_profiles as urp')
                ->select(['urp.oris_id'])
                ->leftJoin('user_entries AS ue', 'ue.user_race_profile_id', '=', 'urp.id')
                ->where('ue.sport_event_id', '=', $sportEvent->id)
                //->where('urp.user_id', '=', auth()->user()->id)
                ->whereNotIn('ue.entry_status', [EntryStatus::Cancel])
                ->get();

            // unsetne z pole relevatnich profil;
            foreach ($entries as $entry) {
                $relevantUserRaceProfile->forget((int)$entry->oris_id);
            }
        } else {
            //Non ORIS race
            $relevantUserRaceProfile = $this->getRelevantRaceProfiles($registerAnyone);

            $relevantUserRaceProfile = $relevantUserRaceProfile->pluck('user_race_full_name', 'id');

            // Has allready signed
            $entries = DB::table('user_race_profiles as urp')
                ->select(['urp.id'])
                ->leftJoin('user_entries AS ue', 'ue.user_race_profile_id', '=', 'urp.id')
                ->where('ue.sport_event_id', '=', $sportEvent->id)
                //->where('urp.user_id', '=', auth()->user()->id)
                ->whereNotIn('ue.entry_status', [EntryStatus::Cancel])
                ->get();

            // Unset from field
            foreach ($entries as $entry) {
                $relevantUserRaceProfile->forget((int)$entry->id);
            }
        }
        return $relevantUserRaceProfile;
    }

    private function getRelevantRaceProfiles(bool $registerAnyone): Collection
    {
        $relevantUserRaceProfile = UserRaceProfile::query()
            ->where('user_id', '=', auth()->user()?->id)
            ->where('active', '=', '1')
            ->orderBy('reg_number')
            ->get();

        if ($registerAnyone) {
            $relevantUserRaceProfile = UserRaceProfile::query()
                ->where('active', '=', '1')
                ->orderBy('reg_number')
                ->get();
        }

        // Add AllowingAnotherUserRaceProfile
        $allowRegisterUserProfile = UserSetting::where('user_id', '=', auth()->user()?->id)
            ->where('type', '=', 'allowRegisterUserProfile')
            ->first();

        if (isset($allowRegisterUserProfile->options['profileIds'])) {
            foreach ($allowRegisterUserProfile->options['profileIds'] as $profileId) {
                $userProfile = UserRaceProfile::query()->where('id', '=', $profileId)->first();
                $relevantUserRaceProfile->add($userProfile);
            }
        }
        return $relevantUserRaceProfile;
    }
}
