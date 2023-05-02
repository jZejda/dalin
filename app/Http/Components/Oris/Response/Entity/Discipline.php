<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

class Discipline
{
    private string $ID;
    private string $ShortName;
    private string $NameCZ;
    private string $NameEN;

    public function __construct(string $ID, string $ShortName, string $NameCZ, string $NameEN)
    {
        $this->ID = $ID;
        $this->ShortName = $ShortName;
        $this->NameCZ = $NameCZ;
        $this->NameEN = $NameEN;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getShortName(): string
    {
        return $this->ShortName;
    }

    public function getNameCZ(): string
    {
        return $this->NameCZ;
    }

    public function getNameEN(): string
    {
        return $this->NameEN;
    }
}
