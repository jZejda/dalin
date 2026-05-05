<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Enums\EntryStatus;
use App\Http\Components\Oris\Response\CreateEntry;
use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Support\Carbon;

class EntryPersister
{
    /**
     * Persist a new UserEntry to the database.
     *
     * Returns null when required relations are missing and no entry can be created.
     *
     * @param array<string, mixed> $data
     * @throws \Throwable
     */
    public function persist(
        bool $isOrisEvent,
        SportEvent $sportEvent,
        ?UserRaceProfile $userRaceProfile,
        ?SportClass $sportClass,
        array $data,
        ?CreateEntry $orisResponse = null,
    ): ?UserEntry {
        if ($userRaceProfile === null || $sportClass === null || $sportClass->classDefinition === null) {
            return null;
        }

        $entry = new UserEntry();

        if ($isOrisEvent) {
            $entry->oris_entry_id = $orisResponse?->Data?->Entry?->ID;
        }

        $entry->sport_event_id = $sportEvent->id;
        $entry->user_race_profile_id = $userRaceProfile->id;
        $entry->class_definition_id = $sportClass->classDefinition->id;
        $entry->class_name = $sportClass->name ?? 'N/A';
        $entry->note = $data['note'];
        $entry->club_note = $data['club_note'];
        $entry->requested_start = $data['requested_start'];
        $entry->si = $data['si'];
        $entry->rent_si = $data['rent_si'] ?? 0;
        $entry->entry_created = Carbon::now();
        $entry->entry_status = EntryStatus::Create;

        if (isset($data['entry_stages'])) {
            $entry->entry_stages = $data['entry_stages'];
        }

        if ($entry->saveOrFail()) {
            return $entry;
        }

        return null;
    }
}
