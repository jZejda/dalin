<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class EventEntries
{
    public function __construct(
        public string $ID,
        public string $ClassDesc,
        public string $RegNo,
        public string $Name,
        public string $FirstName,
        public string $LastName,
        public ?string $RentSI,
        public ?string $SI,
        public ?string $Licence,
        public ?string $RequestedStart,
        public ?string $UserID,
        public ?string $ClubUserID,
        public ?string $ClubID,
        public ?string $Note,
        public ?string $Fee,
        public ?string $EntryStop,
        public ?string $CreatedDateTime,
        public ?string $CreatedByUserID,
        public ?string $UpdatedDateTime,
        public ?string $UpdatedByUserID,
    ) {
    }
}
