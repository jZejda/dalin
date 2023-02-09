<?php

namespace App\Http\Components\Oris\Response\Entity;

class Sport
{
    private string $ID;
    private string $NameCZ;
    private string $NameEN;

    public function __construct(string $ID, string $NameCZ, string $NameEN)
    {
        $this->ID = $ID;
        $this->NameCZ = $NameCZ;
        $this->NameEN = $NameEN;
    }

    public function getID(): string
    {
        return $this->ID;
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
