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

    public const string CACHE_KEY = 'oris.club-members-si.v2';

    public const int CACHE_TTL_SECONDS = 600;

    private const int CONNECT_TIMEOUT_SECONDS = 5;

    private const int TIMEOUT_SECONDS = 20;

    public const string EDIT_METHOD = 'editPerson';

    /** editPerson fields that ORIS requires - they are never sent blank. */
    public const array REQUIRED_PERSON_FIELDS = ['firstname', 'lastname', 'email', 'street', 'city', 'zip', 'country'];

    /** editPerson field => getClubUserList field. */
    private const array PERSON_FIELDS = [
        'firstname' => 'FirstName',
        'lastname' => 'LastName',
        'email' => 'Email',
        'street' => 'Street',
        'city' => 'City',
        'zip' => 'Zip',
        'country' => 'Country',
        'phone' => 'Phone',
        'gpslat' => 'AddressGPSLat',
        'gpslon' => 'AddressGPSLon',
        'iofid' => 'IOFID',
    ];

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
            // The caller needs the person record (it is written back to ORIS), so it is returned
            // from the fresh read but never stored - the cache only holds chips.
            $members = $this->fetch();
            Cache::put(self::CACHE_KEY, $this->toCache($members), self::CACHE_TTL_SECONDS);

            return $members;
        }

        // Only plain arrays go to the cache: Laravel does not unserialize custom classes from it.
        /** @var array<int, array<string, mixed>> $cached */
        $cached = Cache::get(self::CACHE_KEY);

        if (! is_array($cached)) {
            return $this->clubMembers(true);
        }

        return array_map(fn (array $data): OrisClubMember => OrisClubMember::fromArray($data), $cached);
    }

    /**
     * @param  array<int, OrisClubMember>  $members
     * @return array<int, array<string, mixed>>
     */
    private function toCache(array $members): array
    {
        return array_map(fn (OrisClubMember $member): array => $member->withoutPerson()->toArray(), $members);
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

        $payload = $this->post(self::METHOD);

        if (($payload['Status'] ?? null) !== 'OK' || ! is_array($payload['Data']['ClubMembers'] ?? null)) {
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
                $this->parsePerson($row),
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

    public function isConfigured(): bool
    {
        return ! config('demo.enabled') && filled(config('site-config.oris_credentials.general.clubkey'));
    }

    /**
     * Person fields as ORIS holds them. editPerson has no partial update known to be safe, so the
     * whole record is sent back and only the chips are replaced - blank values are left out.
     *
     * @param  array<mixed>  $row
     * @return array<string, string>
     */
    private function parsePerson(array $row): array
    {
        $person = [];

        foreach (self::PERSON_FIELDS as $field => $orisField) {
            $value = trim((string) ($row[$orisField] ?? ''));

            // ORIS reports unset numeric fields (GPS, IOF ID) as 0
            if ($value !== '' && $value !== '0') {
                $person[$field] = $value;
            }
        }

        return $person;
    }

    /**
     * Parameters of an editPerson call that replaces the chips of the person with the given ones
     * and keeps every other field as ORIS currently has it.
     *
     * @param  list<SiChipData>  $chips  at most 3 (ORIS slots)
     * @return array<string, string|int>
     *
     * @throws OrisSiException when ORIS does not hold all required person fields or there are too many chips
     */
    public function buildEditPayload(OrisClubMember $member, array $chips): array
    {
        if (count($chips) > self::SLOTS) {
            throw new OrisSiException(__('si-chip.errors.too_many_chips', ['max' => self::SLOTS]));
        }

        $missing = array_values(array_filter(
            self::REQUIRED_PERSON_FIELDS,
            fn (string $field): bool => ! isset($member->person[$field]),
        ));

        if ($missing !== []) {
            throw new OrisSiException(__('si-chip.errors.person_incomplete', ['fields' => implode(', ', $missing)]));
        }

        $payload = ['userid' => $member->userId] + $member->person;

        for ($slot = 1; $slot <= self::SLOTS; $slot++) {
            $suffix = $slot === 1 ? '' : (string) $slot;
            $chip = $chips[$slot - 1] ?? null;

            $payload['si'.$suffix] = $chip instanceof SiChipData ? $chip->si : '';
            $payload['sisport'.$suffix] = $chip instanceof SiChipData ? $chip->sport->value : 0;
            $payload['sitype'.$suffix] = $chip instanceof SiChipData ? $chip->type->value : 0;
        }

        return $payload;
    }

    /**
     * Replace the chips of a person in ORIS.
     *
     * @param  list<SiChipData>  $chips
     *
     * @throws OrisSiException
     */
    public function editChips(OrisClubMember $member, array $chips): void
    {
        $payload = $this->buildEditPayload($member, $chips);

        $response = $this->post(self::EDIT_METHOD, $payload);

        if (($response['Status'] ?? null) !== 'OK') {
            throw new OrisSiException(__('si-chip.errors.edit_failed', ['status' => (string) ($response['Status'] ?? '?')]));
        }

        $this->forget();
    }

    /**
     * @param  array<string, string|int>  $params
     * @return array<mixed>
     *
     * @throws OrisSiException
     */
    protected function post(string $method, array $params = []): array
    {
        try {
            $client = new GuzzleClient();
            $response = $client->create()->request('POST', 'API', $client->generateMultipartForm($method, $params) + [
                'connect_timeout' => self::CONNECT_TIMEOUT_SECONDS,
                'timeout' => self::TIMEOUT_SECONDS,
            ]);
            $payload = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new OrisSiException(__('si-chip.errors.request_failed'), 0, $e);
        }

        return is_array($payload) ? $payload : [];
    }
}
