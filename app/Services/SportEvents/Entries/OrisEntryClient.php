<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Http\Components\Oris\GuzzleClient;
use App\Http\Components\Oris\ManageEntry;
use App\Http\Components\Oris\Response\CreateEntry;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;

class OrisEntryClient
{
    /**
     * Send a createEntry request to ORIS and return the parsed response.
     *
     * Stage params are built from entry_stages array if the event has stages.
     *
     * @param array<string, mixed> $entryData
     */
    public function createEntry(array $entryData, UserRaceProfile $userProfile, SportEvent $sportEvent): CreateEntry
    {
        $requiredParams = [
            'clubuser' => $userProfile->club_user_id ?? '',
            'class' => $entryData['classId'],
        ];

        $allOptionalParams = [
            'si' => $entryData['si'],
            'note' => $entryData['note'],
            'clubnote' => $entryData['club_note'],
            'rent_si' => $entryData['rent_si'],
            'requested_start' => $entryData['requested_start'],
        ];

        if (isset($entryData['entry_stages'])) {
            for ($stage = 1; $stage <= $sportEvent->stages; $stage++) {
                if (in_array('stage'.$stage, $entryData['entry_stages'], true)) {
                    $allOptionalParams['stage'.$stage] = '1';
                }
                // ORIS only cares if stageX is sent – sending '1' vs nothing is identical.
            }
        }

        $optionalParams = array_filter($allOptionalParams, fn (mixed $v): bool => $v !== null);
        $params = array_merge($requiredParams, $optionalParams);

        return $this->request(GuzzleClient::METHOD_CREATE_ENTRY, $params);
    }

    /**
     * Send a deleteEntry request to ORIS and return the parsed response.
     */
    public function deleteEntry(int $orisEntryId): CreateEntry
    {
        return $this->request(GuzzleClient::METHOD_DELETE_ENTRY, ['entryid' => $orisEntryId]);
    }

    private function request(string $method, array $params): CreateEntry
    {
        $guzzleClient = new GuzzleClient();
        $clientResponse = $guzzleClient->create()->request(
            'POST',
            'API',
            $guzzleClient->generateMultipartForm($method, $params)
        );

        return (new ManageEntry())->data($clientResponse->getBody()->getContents());
    }
}
