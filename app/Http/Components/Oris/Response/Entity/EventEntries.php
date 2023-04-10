<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

class EventEntries
{
    private string $ID;
    private string $ClassDesc;
    private string $RegNo;
    private string $Name;
    private string $FirstName;
    private string $LastName;
    private ?string $RentSI;
    private ?string $SI;
    private ?string $Licence;
    private ?string $RequestedStart;
    private ?string $UserID;
    private ?string $ClubUserID;
    private ?string $ClubID;
    private ?string $Note;
    private ?string $Fee;
    private ?string $EntryStop;
    private ?string $CreatedDateTime;
    private ?string $CreatedByUserID;
    private ?string $UpdatedDateTime;
    private ?string $UpdatedByUserID;

    public function __construct(string $ID, string $ClassDesc, string $RegNo, string $Name, string $FirstName, string $LastName, ?string $RentSI, ?string $SI, ?string $Licence, ?string $RequestedStart, ?string $UserID, ?string $ClubUserID, ?string $ClubID, ?string $Note, ?string $Fee, ?string $EntryStop, ?string $CreatedDateTime, ?string $CreatedByUserID, ?string $UpdatedDateTime, ?string $UpdatedByUserID)
    {
        $this->ID = $ID;
        $this->ClassDesc = $ClassDesc;
        $this->RegNo = $RegNo;
        $this->Name = $Name;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->RentSI = $RentSI;
        $this->SI = $SI;
        $this->Licence = $Licence;
        $this->RequestedStart = $RequestedStart;
        $this->UserID = $UserID;
        $this->ClubUserID = $ClubUserID;
        $this->ClubID = $ClubID;
        $this->Note = $Note;
        $this->Fee = $Fee;
        $this->EntryStop = $EntryStop;
        $this->CreatedDateTime = $CreatedDateTime;
        $this->CreatedByUserID = $CreatedByUserID;
        $this->UpdatedDateTime = $UpdatedDateTime;
        $this->UpdatedByUserID = $UpdatedByUserID;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getClassDesc(): string
    {
        return $this->ClassDesc;
    }

    public function getRegNo(): string
    {
        return $this->RegNo;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getFirstName(): string
    {
        return $this->FirstName;
    }

    public function getLastName(): string
    {
        return $this->LastName;
    }

    public function getRentSI(): ?string
    {
        return $this->RentSI;
    }

    public function getSI(): ?string
    {
        return $this->SI;
    }

    public function getLicence(): ?string
    {
        return $this->Licence;
    }

    public function getRequestedStart(): ?string
    {
        return $this->RequestedStart;
    }

    public function getUserID(): ?string
    {
        return $this->UserID;
    }

    public function getClubUserID(): ?string
    {
        return $this->ClubUserID;
    }

    public function getClubID(): ?string
    {
        return $this->ClubID;
    }

    public function getNote(): ?string
    {
        return $this->Note;
    }

    public function getFee(): ?string
    {
        return $this->Fee;
    }

    public function getEntryStop(): ?string
    {
        return $this->EntryStop;
    }

    public function getCreatedDateTime(): ?string
    {
        return $this->CreatedDateTime;
    }

    public function getCreatedByUserID(): ?string
    {
        return $this->CreatedByUserID;
    }

    public function getUpdatedDateTime(): ?string
    {
        return $this->UpdatedDateTime;
    }

    public function getUpdatedByUserID(): ?string
    {
        return $this->UpdatedByUserID;
    }
}
