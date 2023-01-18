<?php

namespace App\Http\Components\Oris\Response\Entity;

class Classes
{

    public int $ID;
    public string $Name;
    public string $Distance;
    public string $Climbing;
    public int $Controls;
    public ClassDefinition $ClassDefinition;
    public float $Fee;

    public function __construct(int $ID, string $Name, string $Distance, string $Climbing, int $Controls, ClassDefinition $ClassDefinition, float $Fee)
    {
        $this->ID = $ID;
        $this->Name = $Name;
        $this->Distance = $Distance;
        $this->Climbing = $Climbing;
        $this->Controls = $Controls;
        $this->ClassDefinition = $ClassDefinition;
        $this->Fee = $Fee;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getDistance(): string
    {
        return $this->Distance;
    }

    public function getClimbing(): string
    {
        return $this->Climbing;
    }

    public function getControls(): int
    {
        return $this->Controls;
    }

    public function getClassDefinition(): ClassDefinition
    {
        return $this->ClassDefinition;
    }

    public function getFee(): float
    {
        return $this->Fee;
    }
}
