<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Course
{
    private string $Id;
    private string $Name;
    private string $Length;
    private string $Climb;
    private string $NumberOfControls;

    public function __construct(string $Id, string $Name, string $Length, string $Climb, string $NumberOfControls)
    {
        $this->Id = $Id;
        $this->Name = $Name;
        $this->Length = $Length;
        $this->Climb = $Climb;
        $this->NumberOfControls = $NumberOfControls;
    }

    public function getId(): string
    {
        return $this->Id;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getLength(): string
    {
        return $this->Length;
    }

    public function getClimb(): string
    {
        return $this->Climb;
    }

    public function getNumberOfControls(): string
    {
        return $this->NumberOfControls;
    }
}
