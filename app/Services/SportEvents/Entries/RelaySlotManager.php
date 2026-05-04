<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Models\RelayTeamMember;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelaySlotManager
{
    /**
     * Reserve a free relay slot and create a UserEntry for the profile.
     *
     * Uses a pessimistic lock to prevent double-booking under concurrency.
     *
     * @param array<string, mixed> $data
     * @throws \Throwable
     */
    public function reserveSlot(
        SportEvent $sportEvent,
        ?UserRaceProfile $userRaceProfile,
        array $data,
        EntryPersister $persister,
    ): bool {
        if ($userRaceProfile === null || ! isset($data['relayTeamMemberId'])) {
            return false;
        }

        return DB::transaction(function () use ($data, $sportEvent, $userRaceProfile, $persister): bool {
            $relayTeamMember = RelayTeamMember::query()
                ->where('id', (int) $data['relayTeamMemberId'])
                ->whereNull('user_entry_id')
                ->with(['relayTeam.sportClass.classDefinition'])
                ->lockForUpdate()
                ->first();

            if ($relayTeamMember === null || $relayTeamMember->relayTeam->sport_event_id !== $sportEvent->id) {
                return false;
            }

            $sportClass = $relayTeamMember->relayTeam->sportClass;
            $entry = $persister->persist(false, $sportEvent, $userRaceProfile, $sportClass, $data);

            if ($entry === null) {
                return false;
            }

            $relayTeamMember->user_race_profile_id = $userRaceProfile->id;
            $relayTeamMember->user_entry_id = $entry->id;

            return $relayTeamMember->saveOrFail();
        });
    }

    /**
     * Release the relay slot bound to a UserEntry (no-op if not a relay entry).
     */
    public function releaseSlot(UserEntry $userEntry): void
    {
        $relayTeamMember = $userEntry->relayTeamMember;
        if ($relayTeamMember === null) {
            return;
        }

        $relayTeamMember->user_race_profile_id = null;
        $relayTeamMember->user_entry_id = null;
        $relayTeamMember->save();
    }

    /**
     * Return free relay member slots for the given event, ordered by team name + slot.
     *
     * @return Collection<int, string>
     */
    public function availableSlots(SportEvent $sportEvent): Collection
    {
        return RelayTeamMember::query()
            ->whereNull('user_entry_id')
            ->whereHas('relayTeam', fn (Builder $query): Builder => $query->where('sport_event_id', $sportEvent->id))
            ->with(['relayTeam', 'relayTeam.sportClass'])
            ->get()
            ->sortBy([
                fn (RelayTeamMember $member) => $member->relayTeam->name,
                fn (RelayTeamMember $member) => $member->slot,
            ])
            ->mapWithKeys(function (RelayTeamMember $member): array {
                $teamName = $member->relayTeam->name;
                $category = $member->relayTeam->sportClass?->name;
                $label = '<span class="font-medium">'.e($teamName).' - slot '.e((string) $member->slot).'</span>';
                if ($category !== null) {
                    $label .= ' <span class="text-gray-400">| '.e($category).'</span>';
                }

                return [$member->id => $label];
            });
    }
}
