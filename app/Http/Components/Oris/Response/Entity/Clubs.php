<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

class Clubs
{
    private string $ID;
    private string $Name;
    private string $Abbr;
    private string $Region;
    private string $Number;

    public function __construct(string $ID, string $Name, string $Abbr, string $Region, string $Number)
    {
        $this->ID = $ID;
        $this->Name = $Name;
        $this->Abbr = $Abbr;
        $this->Region = $Region;
        $this->Number = $Number;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getAbbr(): string
    {
        return $this->Abbr;
    }

    public function getRegion(): string
    {
        return $this->Region;
    }

    public function getNumber(): string
    {
        return $this->Number;
    }
}
