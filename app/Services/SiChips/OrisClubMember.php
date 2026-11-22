<?php

declare(strict_types=1);

namespace App\Services\SiChips;

/**
 * Club member as returned by ORIS getClubUserList (only the fields needed for SI chips).
 */
final readonly class OrisClubMember
{
    /**
     * @param  list<SiChipData>  $chips  non-empty ORIS chip slots in slot order (SI, SI2, SI3)
     */
    public function __construct(
        public int $userId,
        public int $clubUserId,
        public string $regNum,
        public array $chips,
    ) {
    }
}
