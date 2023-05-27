<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response;

use App\Http\Components\Oris\Response\Entity\Discipline;
use App\Http\Components\Oris\Response\Entity\Level;
use App\Http\Components\Oris\Response\Entity\Org;
use App\Http\Components\Oris\Response\Entity\Sport;

class OrisEvent
{
    public int $ID;
    public string $Name;
    public string $Date;
    public string $Place;
    public ?string $Map;
    public Org $Org1;
    public ?Org $Org2;
    public string $Region;
    public string $EntryDate1;
    public ?string $EntryDate2;
    public ?string $EntryDate3;
    public string $EntryInfo;
    public string $Currency;
    public string $Ranking;
    public ?string $StartTime;
    public ?string $GPSLat;
    public ?string $GPSLon;
    public ?string $EventInfo;
    public ?string $EventWarning;
    public ?string $Cancelled;
    public ?string $Stages;
    public ?string $Stage1;
    public ?string $Stage2;
    public ?string $Stage3;
    public ?string $Stage4;
    public ?string $Stage5;
    public ?string $Stage6;
    public ?string $Stage7;
    public ?string $MultiEvents;
    public ?string $MultiEvent1;
    public ?string $MultiEvent2;
    public ?string $MultiEvent3;
    public ?string $ParentID;
    public array $Services;
    public Sport $Sport;
    public Discipline $Discipline;
    public Level $Level;

    public function __construct(int $ID, string $Name, string $Date, string $Place, ?string $Map, Org $Org1, ?Org $Org2, string $Region, string $EntryDate1, ?string $EntryDate2, ?string $EntryDate3, string $EntryInfo, string $Currency, string $Ranking, ?string $StartTime, ?string $GPSLat, ?string $GPSLon, ?string $EventInfo, ?string $EventWarning, ?string $Cancelled, ?string $Stages, ?string $Stage1, ?string $Stage2, ?string $Stage3, ?string $Stage4, ?string $Stage5, ?string $Stage6, ?string $Stage7, ?string $MultiEvents, ?string $MultiEvent1, ?string $MultiEvent2, ?string $MultiEvent3, ?string $ParentID, array $Services, Sport $Sport, Discipline $Discipline, Level $Level)
    {
        $this->ID = $ID;
        $this->Name = $Name;
        $this->Date = $Date;
        $this->Place = $Place;
        $this->Map = $Map;
        $this->Org1 = $Org1;
        $this->Org2 = $Org2;
        $this->Region = $Region;
        $this->EntryDate1 = $EntryDate1;
        $this->EntryDate2 = $EntryDate2;
        $this->EntryDate3 = $EntryDate3;
        $this->EntryInfo = $EntryInfo;
        $this->Currency = $Currency;
        $this->Ranking = $Ranking;
        $this->StartTime = $StartTime;
        $this->GPSLat = $GPSLat;
        $this->GPSLon = $GPSLon;
        $this->EventInfo = $EventInfo;
        $this->EventWarning = $EventWarning;
        $this->Cancelled = $Cancelled;
        $this->Stages = $Stages;
        $this->Stage1 = $Stage1;
        $this->Stage2 = $Stage2;
        $this->Stage3 = $Stage3;
        $this->Stage4 = $Stage4;
        $this->Stage5 = $Stage5;
        $this->Stage6 = $Stage6;
        $this->Stage7 = $Stage7;
        $this->MultiEvents = $MultiEvents;
        $this->MultiEvent1 = $MultiEvent1;
        $this->MultiEvent2 = $MultiEvent2;
        $this->MultiEvent3 = $MultiEvent3;
        $this->ParentID = $ParentID;
        $this->Services = $Services;
        $this->Sport = $Sport;
        $this->Discipline = $Discipline;
        $this->Level = $Level;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getDate(): string
    {
        return $this->Date;
    }

    public function getPlace(): string
    {
        return $this->Place;
    }

    public function getMap(): ?string
    {
        return $this->Map;
    }

    public function getOrg1(): Org
    {
        return $this->Org1;
    }

    public function getOrg2(): ?Org
    {
        return $this->Org2;
    }

    public function getRegion(): string
    {
        return $this->Region;
    }

    public function getEntryDate1(): string
    {
        return $this->EntryDate1;
    }

    public function getEntryDate2(): ?string
    {
        return $this->EntryDate2;
    }

    public function getEntryDate3(): ?string
    {
        return $this->EntryDate3;
    }

    public function getEntryInfo(): string
    {
        return $this->EntryInfo;
    }

    public function getCurrency(): string
    {
        return $this->Currency;
    }

    public function getRanking(): string
    {
        return $this->Ranking;
    }

    public function getStartTime(): ?string
    {
        return $this->StartTime;
    }

    public function getGPSLat(): ?string
    {
        return $this->GPSLat;
    }

    public function getGPSLon(): ?string
    {
        return $this->GPSLon;
    }

    public function getEventInfo(): ?string
    {
        return $this->EventInfo;
    }

    public function getEventWarning(): ?string
    {
        return $this->EventWarning;
    }

    public function getCancelled(): ?string
    {
        return $this->Cancelled;
    }

    public function getStages(): ?string
    {
        return $this->Stages;
    }

    public function getStage1(): ?string
    {
        return $this->Stage1;
    }

    public function getStage2(): ?string
    {
        return $this->Stage2;
    }

    public function getStage3(): ?string
    {
        return $this->Stage3;
    }

    public function getStage4(): ?string
    {
        return $this->Stage4;
    }

    public function getStage5(): ?string
    {
        return $this->Stage5;
    }

    public function getStage6(): ?string
    {
        return $this->Stage6;
    }

    public function getStage7(): ?string
    {
        return $this->Stage7;
    }

    public function getMultiEvents(): ?string
    {
        return $this->MultiEvents;
    }

    public function getMultiEvent1(): ?string
    {
        return $this->MultiEvent1;
    }

    public function getMultiEvent2(): ?string
    {
        return $this->MultiEvent2;
    }

    public function getMultiEvent3(): ?string
    {
        return $this->MultiEvent3;
    }

    public function getParentID(): ?string
    {
        return $this->ParentID;
    }

    public function getServices(): array
    {
        return $this->Services;
    }

    public function getSport(): Sport
    {
        return $this->Sport;
    }

    public function getDiscipline(): Discipline
    {
        return $this->Discipline;
    }

    public function getLevel(): Level
    {
        return $this->Level;
    }
}
