<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Event
{
    private string $Name;

    public function __construct(string $Name)
    {
        $this->Name = $Name;
    }

    public function getName(): string
    {
        return $this->Name;
    }
}
