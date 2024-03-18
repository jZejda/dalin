<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response;

use App\Http\Components\Oris\Response\Entity\Discipline;
use App\Http\Components\Oris\Response\Entity\Level;
use App\Http\Components\Oris\Response\Entity\Org;
use App\Http\Components\Oris\Response\Entity\Sport;

readonly class OrisEvent
{
    public function __construct(
        public string $ID,
        public string $Name,
        public string $Date,
        public string $Place,
        public ?string $Map,
        public Org $Org1,
        public ?Org $Org2,
        public string $Region,
        public string $EntryDate1,
        public ?string $EntryDate2,
        public ?string $EntryDate3,
        public ?string $EntryKoef2,
        public ?string $EntryKoef3,
        public string $EntryInfo,
        public string $Currency,
        public string $Ranking,
        public ?string $StartTime,
        public ?string $GPSLat,
        public ?string $GPSLon,
        public ?string $EventInfo,
        public ?string $EventWarning,
        public ?string $Cancelled,
        public ?string $Stages,
        public ?string $Stage1,
        public ?string $Stage2,
        public ?string $Stage3,
        public ?string $Stage4,
        public ?string $Stage5,
        public ?string $Stage6,
        public ?string $Stage7,
        public ?string $MultiEvents,
        public ?string $MultiEvent1,
        public ?string $MultiEvent2,
        public ?string $MultiEvent3,
        public ?string $ParentID,
        public array $Services,
        public Sport $Sport,
        public Discipline $Discipline,
        public Level $Level,
    ) {
    }
}
