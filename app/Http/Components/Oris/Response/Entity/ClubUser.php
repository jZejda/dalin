<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

final class ClubUser
{
    private string $ID;
    private string $ClubID;
    private string $MemberFrom;
    private string $MemberTo;
    private string $RegNo;

    public function __construct(string $ID, string $ClubID, string $MemberFrom, string $MemberTo, string $RegNo)
    {
        $this->ID = $ID;
        $this->ClubID = $ClubID;
        $this->MemberFrom = $MemberFrom;
        $this->MemberTo = $MemberTo;
        $this->RegNo = $RegNo;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getClubID(): string
    {
        return $this->ClubID;
    }

    public function getMemberFrom(): string
    {
        return $this->MemberFrom;
    }

    public function getMemberTo(): string
    {
        return $this->MemberTo;
    }

    public function getRegNo(): string
    {
        return $this->RegNo;
    }
}
