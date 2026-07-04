<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

use App\Http\Components\Oris\GuzzleClient;
use App\Models\SportService;
use App\Models\UserRaceProfile;

class OrisServiceEntryClient
{
    /**
     * Send a createServiceEntry request to ORIS and return the parsed response.
     */
    public function createServiceEntry(SportService $service, UserRaceProfile $userProfile, int $qty, ?string $note = null): OrisServiceEntryResponse
    {
        $params = [
            'clubuser' => $userProfile->club_user_id ?? '',
            'service' => $service->oris_service_id,
            'qty' => $qty,
        ];

        if ($note !== null && $note !== '') {
            $params['note'] = $note;
        }

        return $this->request(GuzzleClient::METHOD_CREATE_SERVICE_ENTRY, $params);
    }

    /**
     * Send a deleteServiceEntry request to ORIS and return the parsed response.
     */
    public function deleteServiceEntry(int $orisServiceEntryId): OrisServiceEntryResponse
    {
        return $this->request(GuzzleClient::METHOD_DELETE_SERVICE_ENTRY, ['serviceentryid' => $orisServiceEntryId]);
    }

    /** @param array<string, mixed> $params */
    private function request(string $method, array $params): OrisServiceEntryResponse
    {
        $guzzleClient = new GuzzleClient();
        $clientResponse = $guzzleClient->create()->request(
            'POST',
            'API',
            $guzzleClient->generateMultipartForm($method, $params)
        );

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($clientResponse->getBody()->getContents(), true) ?? [];

        return new OrisServiceEntryResponse(
            status: (string) ($decoded['Status'] ?? 'ERROR'),
            serviceEntryId: $this->extractServiceEntryId($decoded),
        );
    }

    /** @param array<string, mixed> $decoded */
    private function extractServiceEntryId(array $decoded): ?int
    {
        $data = $decoded['Data'] ?? null;
        if (! is_array($data)) {
            return null;
        }

        // ORIS wraps the created entry id differently per method version, try known shapes.
        $id = $data['ServiceEntry']['ID'] ?? $data['Entry']['ID'] ?? $data['ID'] ?? null;

        return $id !== null ? (int) $id : null;
    }
}
