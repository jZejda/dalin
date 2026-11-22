<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Enums\SiChipSport;
use App\Enums\SiChipType;
use App\Http\Components\Oris\GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

/**
 * Reads SI chips of the club members from ORIS (getClubUserList, needs the club key).
 */
class OrisSiClient
{
    public const string METHOD = 'getClubUserList';

    public const string CACHE_KEY = 'oris.club-members-si';

    public const int CACHE_TTL_SECONDS = 600;

    private const int SLOTS = 3;

    /**
     * Club members keyed by ORIS user id. The list is cached because it covers the whole club.
     *
     * @return array<int, OrisClubMember>
     *
     * @throws OrisSiException
     */
    public function clubMembers(bool $fresh = false): array
    {
        if ($fresh) {
            $this->forget();
        }

        /** @var array<int, OrisClubMember> */
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn (): array => $this->fetch());
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<int, OrisClubMember>
     *
     * @throws OrisSiException
     */
    private function fetch(): array
    {
        if (blank(config('site-config.oris_credentials.general.clubkey'))) {
            throw new OrisSiException(__('si-chip.errors.no_club_key'));
        }

        try {
            $client = new GuzzleClient();
            $response = $client->create()->request('POST', 'API', $client->generateMultipartForm(self::METHOD));
            $payload = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new OrisSiException(__('si-chip.errors.request_failed'), 0, $e);
        }

        if (! is_array($payload) || ($payload['Status'] ?? null) !== 'OK' || ! is_array($payload['Data']['ClubMembers'] ?? null)) {
            throw new OrisSiException(__('si-chip.errors.invalid_response'));
        }

        return $this->parseMembers($payload['Data']['ClubMembers']);
    }

    /**
     * @param  array<mixed>  $rows
     * @return array<int, OrisClubMember>
     */
    public function parseMembers(array $rows): array
    {
        $members = [];

        foreach ($rows as $row) {
            if (! is_array($row) || ! isset($row['UserID'])) {
                continue;
            }

            $userId = (int) $row['UserID'];

            // A person can be listed once per club membership; the first row wins.
            if (isset($members[$userId])) {
                continue;
            }

            $members[$userId] = new OrisClubMember(
                $userId,
                (int) ($row['ID'] ?? 0),
                (string) ($row['RegNum'] ?? ''),
                $this->parseChips($row),
            );
        }

        return $members;
    }

    /**
     * @param  array<mixed>  $row
     * @return list<SiChipData>
     */
    private function parseChips(array $row): array
    {
        $chips = [];

        for ($slot = 1; $slot <= self::SLOTS; $slot++) {
            $suffix = $slot === 1 ? '' : (string) $slot;
            $si = (int) ($row['SI'.$suffix] ?? 0);

            if ($si <= 0) {
                continue;
            }

            $chips[] = new SiChipData(
                $si,
                SiChipSport::tryFrom((int) ($row['SISport'.$suffix] ?? 0)) ?? SiChipSport::All,
                SiChipType::tryFrom((int) ($row['SIType'.$suffix] ?? 0)) ?? SiChipType::All,
            );
        }

        return $chips;
    }
}
