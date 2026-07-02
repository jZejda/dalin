<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries\Strategy;

use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Entries\EntryPersister;
use App\Services\SportEvents\Entries\EntryResult;
use App\Services\SportEvents\Entries\RelaySlotManager;

class RelayEntryStrategy implements EntryStrategy
{
    public function __construct(
        private RelaySlotManager $slotManager,
        private EntryPersister $persister,
    ) {
    }

    public function canHandle(SportEvent $event): bool
    {
        return $event->isRelayDiscipline();
    }

    /** @param array<string, mixed> $data */
    public function create(SportEvent $event, array $data): EntryResult
    {
        $userRaceProfile = UserRaceProfile::query()->where('id', '=', $data['raceProfileId'])->first();

        $success = $this->slotManager->reserveSlot($event, $userRaceProfile, $data, $this->persister);

        return new EntryResult(success: $success);
    }
}
