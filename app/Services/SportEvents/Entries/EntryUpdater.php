<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Enums\EntryStatus;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Shared\Helpers\EmptyType;

class EntryUpdater
{
    public function __construct(
        private OrisEntryClient $orisClient,
    ) {
    }

    /**
     * Update a UserEntry. If it has an ORIS entry ID, the change is first
     * pushed to ORIS and persisted locally only when ORIS confirms it.
     *
     * @param array<string, mixed> $data
     * @throws \Throwable
     */
    public function update(UserEntry $userEntry, array $data): UpdateResult
    {
        $sportEvent = $userEntry->sportEvent;
        $wasOrisEntry = EmptyType::intNotEmpty($userEntry->oris_entry_id);

        if (! $sportEvent instanceof SportEvent) {
            return new UpdateResult(success: false, wasOrisEntry: $wasOrisEntry);
        }

        $sportClass = $this->resolveSportClass($sportEvent, $data);
        $classDefinition = $sportClass?->classDefinition;

        if ($sportClass === null || $classDefinition === null) {
            return new UpdateResult(success: false, wasOrisEntry: $wasOrisEntry);
        }

        if ($wasOrisEntry) {
            return $this->updateOrisEntry($userEntry, $sportEvent, $sportClass, $classDefinition, $data);
        }

        $this->persistChanges($userEntry, $sportClass, $classDefinition, $data);

        return new UpdateResult(success: true, wasOrisEntry: false);
    }

    /**
     * The form sends ORIS class IDs for ORIS events and local IDs otherwise.
     *
     * @param array<string, mixed> $data
     */
    private function resolveSportClass(SportEvent $sportEvent, array $data): ?SportClass
    {
        $query = SportClass::query()->where('sport_event_id', '=', $sportEvent->id);

        if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
            return $query->where('oris_id', '=', $data['classId'] ?? null)->first();
        }

        return $query->where('id', '=', $data['classId'] ?? null)->first();
    }

    /**
     * @param array<string, mixed> $data
     * @throws \Throwable
     */
    private function updateOrisEntry(
        UserEntry $userEntry,
        SportEvent $sportEvent,
        SportClass $sportClass,
        SportClassDefinition $classDefinition,
        array $data,
    ): UpdateResult {
        $orisResponse = $this->orisClient->updateEntry($data, (int) $userEntry->oris_entry_id, $sportEvent);

        if ($orisResponse->Status !== 'OK') {
            return new UpdateResult(
                success: false,
                wasOrisEntry: true,
                orisEventId: $sportEvent->oris_id,
                orisStatusError: $orisResponse->Status,
            );
        }

        $this->persistChanges($userEntry, $sportClass, $classDefinition, $data);

        return new UpdateResult(success: true, wasOrisEntry: true, orisEventId: $sportEvent->oris_id);
    }

    /**
     * @param array<string, mixed> $data
     * @throws \Throwable
     */
    private function persistChanges(
        UserEntry $userEntry,
        SportClass $sportClass,
        SportClassDefinition $classDefinition,
        array $data,
    ): void {
        $userEntry->class_definition_id = $classDefinition->id;
        $userEntry->class_name = $sportClass->name ?? 'N/A';
        $userEntry->note = $data['note'] ?? null;
        $userEntry->club_note = $data['club_note'] ?? null;
        $userEntry->requested_start = $data['requested_start'] ?? null;
        $userEntry->si = $data['si'] ?? null;
        $userEntry->rent_si = $data['rent_si'] ?? 0;
        $userEntry->entry_status = EntryStatus::Edit;

        if (array_key_exists('entry_stages', $data)) {
            $userEntry->entry_stages = $data['entry_stages'];
        }

        $userEntry->saveOrFail();
    }

    public static function make(): self
    {
        return new self(new OrisEntryClient());
    }
}
