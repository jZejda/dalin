<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Enums\EntryStatus;
use App\Models\UserEntry;
use App\Shared\Helpers\EmptyType;

class EntryDeleter
{
    public function __construct(
        private OrisEntryClient $orisClient,
        private RelaySlotManager $slotManager,
    ) {
    }

    /**
     * Cancel a UserEntry. If it has an ORIS entry ID, also deletes it from ORIS.
     *
     * @throws \Throwable
     */
    public function delete(UserEntry $userEntry): DeleteResult
    {
        if (EmptyType::intNotEmpty($userEntry->oris_entry_id)) {
            return $this->deleteOrisEntry($userEntry);
        }

        return $this->deleteLocalEntry($userEntry);
    }

    /** @throws \Throwable */
    private function deleteOrisEntry(UserEntry $userEntry): DeleteResult
    {
        $orisEventId = $userEntry->sportEvent?->oris_id;
        $orisResponse = $this->orisClient->deleteEntry((int) $userEntry->oris_entry_id);

        if ($orisResponse->Status !== 'OK') {
            return new DeleteResult(
                success: false,
                wasOrisEntry: true,
                orisStatusError: $orisResponse->Status,
            );
        }

        $userEntry->entry_status = EntryStatus::Cancel;
        $userEntry->saveOrFail();
        $this->slotManager->releaseSlot($userEntry);

        return new DeleteResult(success: true, wasOrisEntry: true, orisEventId: $orisEventId);
    }

    /** @throws \Throwable */
    private function deleteLocalEntry(UserEntry $userEntry): DeleteResult
    {
        $userEntry->entry_status = EntryStatus::Cancel;
        $userEntry->saveOrFail();
        $this->slotManager->releaseSlot($userEntry);

        return new DeleteResult(success: true, wasOrisEntry: false);
    }

    public static function make(): self
    {
        return new self(new OrisEntryClient(), new RelaySlotManager());
    }
}
