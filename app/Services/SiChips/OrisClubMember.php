<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Enums\SiChipSport;
use App\Enums\SiChipType;

/**
 * Club member as returned by ORIS getClubUserList (only the fields needed for SI chips).
 */
final readonly class OrisClubMember
{
    /**
     * @param  list<SiChipData>  $chips  non-empty ORIS chip slots in slot order (SI, SI2, SI3)
     * @param  array<string, string>  $person  editPerson fields as ORIS currently holds them (email, street, ...)
     */
    public function __construct(
        public int $userId,
        public int $clubUserId,
        public string $regNum,
        public array $chips,
        public array $person = [],
    ) {
    }

    /**
     * Plain-array form for the cache (Laravel does not restore custom classes from the cache).
     *
     * @return array{userId: int, clubUserId: int, regNum: string, chips: list<array{si: int, sport: int, type: int}>, person: array<string, string>}
     */
    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'clubUserId' => $this->clubUserId,
            'regNum' => $this->regNum,
            'chips' => array_map(fn (SiChipData $chip): array => [
                'si' => $chip->si,
                'sport' => $chip->sport->value,
                'type' => $chip->type->value,
            ], $this->chips),
            'person' => $this->person,
        ];
    }

    /**
     * @param  array<mixed>  $data  result of toArray()
     */
    public static function fromArray(array $data): self
    {
        $chips = [];

        foreach ((array) ($data['chips'] ?? []) as $chip) {
            $chips[] = new SiChipData(
                (int) $chip['si'],
                SiChipSport::tryFrom((int) $chip['sport']) ?? SiChipSport::All,
                SiChipType::tryFrom((int) $chip['type']) ?? SiChipType::All,
            );
        }

        /** @var array<string, string> $person */
        $person = (array) ($data['person'] ?? []);

        return new self(
            (int) $data['userId'],
            (int) $data['clubUserId'],
            (string) $data['regNum'],
            $chips,
            $person,
        );
    }
}
