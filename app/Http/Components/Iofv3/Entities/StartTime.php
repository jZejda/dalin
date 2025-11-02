<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class StartTime
{
    private ?string $Date;
    private ?string $Time;

    public function __construct(?string $Date, ?string $Time)
    {
        $this->Date = $Date;
        $this->Time = $Time;
    }

    public function getDate(): ?string
    {
        return $this->Date;
    }

    public function getTime(): ?string
    {
        return $this->Time;
    }
}
