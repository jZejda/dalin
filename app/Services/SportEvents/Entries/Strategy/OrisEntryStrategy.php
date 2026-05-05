<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries\Strategy;

use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Entries\EntryPersister;
use App\Services\SportEvents\Entries\EntryResult;
use App\Services\SportEvents\Entries\OrisEntryClient;

class OrisEntryStrategy implements EntryStrategy
{
    public function __construct(
        private OrisEntryClient $orisClient,
        private EntryPersister $persister,
    ) {
    }

    public function canHandle(SportEvent $event): bool
    {
        return $event->oris_id !== null && $event->use_oris_for_entries;
    }

    /** @param array<string, mixed> $data */
    public function create(SportEvent $event, array $data): EntryResult
    {
        $userRaceProfile = UserRaceProfile::where('oris_id', '=', $data['raceProfileId'])->first();
        $sportClass = SportClass::where('oris_id', '=', $data['classId'])->first();

        if ($userRaceProfile === null) {
            return new EntryResult(success: false);
        }

        $orisResponse = $this->orisClient->createEntry($data, $userRaceProfile, $event);

        if ($orisResponse->Status !== 'OK') {
            return new EntryResult(
                success: false,
                userRaceProfile: $userRaceProfile,
                sportClass: $sportClass,
                orisStatusError: $orisResponse->Status,
                orisEventId: $event->oris_id,
            );
        }

        $entry = $this->persister->persist(true, $event, $userRaceProfile, $sportClass, $data, $orisResponse);

        return new EntryResult(
            success: $entry !== null,
            entry: $entry,
            userRaceProfile: $userRaceProfile,
            sportClass: $sportClass,
            orisEventId: $event->oris_id,
        );
    }
}
