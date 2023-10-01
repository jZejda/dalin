<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Start
{
    private string $StartTime;
    private string|null $ControlCard;

    /**
     * @param string $StartTime
     * @param string|null $ControlCard
     */
    public function __construct(string $StartTime, ?string $ControlCard)
    {
        $this->StartTime = $StartTime;
        $this->ControlCard = $ControlCard;
    }

    public function getStartTime(): string
    {
        return $this->StartTime;
    }

    public function getControlCard(): ?string
    {
        return $this->ControlCard;
    }

}
