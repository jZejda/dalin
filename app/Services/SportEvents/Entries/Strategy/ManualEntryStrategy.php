<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries\Strategy;

use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Entries\EntryPersister;
use App\Services\SportEvents\Entries\EntryResult;

class ManualEntryStrategy implements EntryStrategy
{
    public function __construct(
        private EntryPersister $persister,
    ) {
    }

    public function canHandle(SportEvent $event): bool
    {
        return true; // fallback — handles all non-ORIS, non-relay events
    }

    /** @param array<string, mixed> $data */
    public function create(SportEvent $event, array $data): EntryResult
    {
        $userRaceProfile = UserRaceProfile::query()->where('id', '=', $data['raceProfileId'])->first();
        $sportClass = SportClass::query()->where('id', '=', $data['classId'])->first();

        $entry = $this->persister->persist(false, $event, $userRaceProfile, $sportClass, $data);

        return new EntryResult(
            success: $entry !== null,
            entry: $entry,
            userRaceProfile: $userRaceProfile,
            sportClass: $sportClass,
        );
    }
}
