<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions\Helpers;

use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Models\UserSetting;
use App\Shared\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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

        // ORIS API neumí štafetové přihlášky – štafety se přihlašují vždy jen interně,
        // proto musí být options klíčované interním id i u ORIS závodů.
        if ($sportEvent->isRelayDiscipline()) {
            return $this->formatProfilesWithStyling($this->getRelevantRaceProfiles($registerAnyone), 'id');
        }

        if ((!is_null($sportEvent->oris_id) && $sportEvent->use_oris_for_entries)) {

            //vyselektuje relevatni profily pro uzivatel
            $relevantUserRaceProfile = $this->getRelevantRaceProfiles($registerAnyone);
            //omezí pouze tam kde je aktuální oris_id
            $relevantUserRaceProfile = $relevantUserRaceProfile->whereNotNull('oris_id');

            // zjisti id profil; ktere jsou uz v zavode pro uzivatele
            $userRaceProfiles = DB::table('user_race_profiles as urp')
                ->select(['urp.oris_id'])
                ->leftJoin('user_entries AS ue', 'ue.user_race_profile_id', '=', 'urp.id')
                ->where('ue.sport_event_id', '=', $sportEvent->id)
                //->where('urp.user_id', '=', auth()->user()->id)
                ->whereNotIn('ue.entry_status', [EntryStatus::Cancel])
                ->get();

            // unsetne z pole relevatnich profil;
            foreach ($userRaceProfiles as $userRaceProfile) {
                $relevantUserRaceProfile = $relevantUserRaceProfile->reject(function (UserRaceProfile $profile) use ($userRaceProfile) {
                    return (int)$profile->oris_id === (int)$userRaceProfile->oris_id;
                });
            }

            $relevantUserRaceProfile = $this->formatProfilesWithStyling($relevantUserRaceProfile, 'oris_id');
        } else {
            //Non ORIS race
            $relevantUserRaceProfile = $this->getRelevantRaceProfiles($registerAnyone);

            // Has allready signed
            $userRaceProfiles = DB::table('user_race_profiles as urp')
                ->select(['urp.id'])
                ->leftJoin('user_entries AS ue', 'ue.user_race_profile_id', '=', 'urp.id')
                ->where('ue.sport_event_id', '=', $sportEvent->id)
                //->where('urp.user_id', '=', auth()->user()->id)
                ->whereNotIn('ue.entry_status', [EntryStatus::Cancel])
                ->get();

            // Unset from field
            foreach ($userRaceProfiles as $userRaceProfile) {
                $relevantUserRaceProfile = $relevantUserRaceProfile->reject(function (UserRaceProfile $profile) use ($userRaceProfile) {
                    return (int)$profile->id === (int)$userRaceProfile->id;
                });
            }

            $relevantUserRaceProfile = $this->formatProfilesWithStyling($relevantUserRaceProfile, 'id');
        }

        return $relevantUserRaceProfile;
    }

    private function getRelevantRaceProfiles(bool $registerAnyone): Collection
    {
        $relevantUserRaceProfile = UserRaceProfile::query()
            ->where('user_id', '=', Auth::user()?->id)
            ->where('active', '=', '1')
            ->orderBy('reg_number')
            ->get();

        if ($registerAnyone) {
            $relevantUserRaceProfile = UserRaceProfile::query()
                ->where('active', '=', '1')
                ->orderBy('reg_number')
                ->get();
        }

        // Add UserRaceProfiles from users that allow signup to other user
        $currentUserId = Auth::user()?->id;
        if ($currentUserId) {
            $userSettings = UserSetting::where('type', '=', 'usersAllowSignForRace')
                ->whereJsonContains('options->users_allow_sign_up_for_race', (string)$currentUserId)
                ->get();

            foreach ($userSettings as $setting) {
                $allowedUserProfiles = UserRaceProfile::query()
                    ->where('user_id', '=', $setting->user_id)
                    ->where('active', '=', '1')
                    ->orderBy('reg_number')
                    ->get();

                $relevantUserRaceProfile = $relevantUserRaceProfile->merge($allowedUserProfiles);
            }
        }

        return $relevantUserRaceProfile;
    }

    private function formatProfilesWithStyling(Collection $profiles, string $userProfileKeyField): Collection
    {
        $currentUserId = Auth::user()?->id;
        $formattedProfiles = new Collection();

        foreach ($profiles as $profile) {
            $key = $profile->$userProfileKeyField;
            $name = $profile->user_race_full_name;

            if ($profile->user_id === $currentUserId) {
                $formattedProfiles->put($key, $name);
            } else {
                $formattedProfiles->put($key, '<span class="text-yellow-600 dark:text-yellow-400">' . $name . '</span>');
            }
        }

        return $formattedProfiles;
    }

    public static function allowAnotherUserCareTheirUserRaceProfile(UserRaceProfile $userRaceProfile): bool
    {
        $user = Auth::user();

        $userSettings = UserSetting::where('type', '=', 'usersAllowSignForRace')
                ->whereJsonContains('options->users_allow_sign_up_for_race', (string)$user?->id)
                ->get();

        foreach ($userSettings as $setting) {
            $allowedUserProfiles = UserRaceProfile::query()
                ->where('user_id', '=', $setting->user_id)
                ->where('active', '=', '1')
                ->get();

            foreach ($allowedUserProfiles as $allowedUserProfile) {
                if ($allowedUserProfile !== null && $userRaceProfile->id === $allowedUserProfile->id) {
                    return true;
                }
            }
        }

        return false;
    }
}
