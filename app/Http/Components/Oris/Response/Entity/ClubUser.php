<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class ClubUser
{
    public function __construct(
        public string $ID,
        public string $ClubID,
        public string $MemberFrom,
        public string $MemberTo,
        public string $RegNo,
    ) {
    }
}
