<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

class ClassDefinition
{
    public int $ID;
    public int $AgeFrom;
    public int $AgeTo;
    public string $Gender;
    public string $Name;

    public function __construct(int $ID, int $AgeFrom, int $AgeTo, string $Gender, string $Name)
    {
        $this->ID = $ID;
        $this->AgeFrom = $AgeFrom;
        $this->AgeTo = $AgeTo;
        $this->Gender = $Gender;
        $this->Name = $Name;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function getAgeFrom(): int
    {
        return $this->AgeFrom;
    }

    public function getAgeTo(): int
    {
        return $this->AgeTo;
    }

    public function getGender(): string
    {
        return $this->Gender;
    }

    public function getName(): string
    {
        return $this->Name;
    }
}
