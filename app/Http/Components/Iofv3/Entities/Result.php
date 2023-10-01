<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Result
{
    private string $StartTime;
    private string|null $FinishTime;
    private string|null $Time;
    private string|null $TimeBehind;
    private string|null $Position;
    private string $Status;
    private string $ControlCard;

    public function __construct(string $StartTime, ?string $FinishTime, ?string $Time, ?string $TimeBehind, ?string $Position, string $Status, string $ControlCard)
    {
        $this->StartTime = $StartTime;
        $this->FinishTime = $FinishTime;
        $this->Time = $Time;
        $this->TimeBehind = $TimeBehind;
        $this->Position = $Position;
        $this->Status = $Status;
        $this->ControlCard = $ControlCard;
    }

    public function getStartTime(): string
    {
        return $this->StartTime;
    }

    public function getFinishTime(): ?string
    {
        return $this->FinishTime;
    }

    public function getTime(): ?string
    {
        return $this->Time;
    }

    public function getTimeBehind(): ?string
    {
        return $this->TimeBehind;
    }

    public function getPosition(): ?string
    {
        return $this->Position;
    }

    public function getStatus(): string
    {
        return $this->Status;
    }

    public function getControlCard(): string
    {
        return $this->ControlCard;
    }
}
