<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

class Org
{
    public int $ID;
    public string $Abbr;
    public string $Name;

    public function __construct(int $ID, string $Abbr, string $Name)
    {
        $this->ID = $ID;
        $this->Abbr = $Abbr;
        $this->Name = $Name;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function getAbbr(): string
    {
        return $this->Abbr;
    }

    public function getName(): string
    {
        return $this->Name;
    }
}
