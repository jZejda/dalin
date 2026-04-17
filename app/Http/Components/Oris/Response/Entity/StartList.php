<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class StartList
{
    public function __construct(
        public string $ID,
        public string $ClassID,
        public string $ClassDesc,
        public string $Name,
        public string $RegNo,
        public ?string $Lic,
        public ?string $ClubNameStartLists,
        public ?string $StartTime,
        public ?string $UserID,
        public ?string $ClubID,
        public ?string $StartNumber,
        public ?string $SI,
    ) {
    }
}
