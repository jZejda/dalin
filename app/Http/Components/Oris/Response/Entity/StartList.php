<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;

final readonly class StartList
{
    public string $ID;
    public string $ClassID;
    public string $ClassDesc;
    public string $Name;
    public string $RegNo;
    public string $Lic;
    public string $ClubNameStartLists;
    public string $StartTime;
    public string $UserID;
    public string $ClubID;
    public string $StartNumber;
    public string $SI;

    public function __construct(string $ID, string $ClassID, string $ClassDesc, string $Name, string $RegNo, string $Lic, string $ClubNameStartLists, string $StartTime, string $UserID, string $ClubID, string $StartNumber, string $SI)
    {
        $this->ID = $ID;
        $this->ClassID = $ClassID;
        $this->ClassDesc = $ClassDesc;
        $this->Name = $Name;
        $this->RegNo = $RegNo;
        $this->Lic = $Lic;
        $this->ClubNameStartLists = $ClubNameStartLists;
        $this->StartTime = $StartTime;
        $this->UserID = $UserID;
        $this->ClubID = $ClubID;
        $this->StartNumber = $StartNumber;
        $this->SI = $SI;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getClassID(): string
    {
        return $this->ClassID;
    }

    public function getClassDesc(): string
    {
        return $this->ClassDesc;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getRegNo(): string
    {
        return $this->RegNo;
    }

    public function getLic(): string
    {
        return $this->Lic;
    }

    public function getClubNameStartLists(): string
    {
        return $this->ClubNameStartLists;
    }

    public function getStartTime(): Carbon|null
    {
        $startTime = Carbon::createFromFormat(AppHelper::MYSQL_DATE_TIME, $this->StartTime);

        if ($startTime !== false) {
            return $startTime;
        }

        return null;
    }

    public function getUserID(): string
    {
        return $this->UserID;
    }

    public function getClubID(): string
    {
        return $this->ClubID;
    }

    public function getStartNumber(): string
    {
        return $this->StartNumber;
    }

    public function getSI(): string
    {
        return $this->SI;
    }
}
