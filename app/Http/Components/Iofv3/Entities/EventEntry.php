<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class EventEntry
{
    private string $Name;
    private ?StartTime $StartTime;

    public function __construct(string $Name, ?StartTime $StartTime = null)
    {
        $this->Name = $Name;
        $this->StartTime = $StartTime;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getStartTime(): ?StartTime
    {
        return $this->StartTime;
    }
}
