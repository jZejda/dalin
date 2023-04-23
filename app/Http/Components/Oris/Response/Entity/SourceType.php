<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

final class SourceType
{
    public int $ID;
    public string|null $NameCZ;
    public string|null $NameEN;

    public function __construct (int $ID, ?string $NameCZ, ?string $NameEN)
    {
        $this->ID = $ID;
        $this->NameCZ = $NameCZ;
        $this->NameEN = $NameEN;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function getNameCZ(): ?string
    {
        return $this->NameCZ;
    }

    public function getNameEN(): ?string
    {
        return $this->NameEN;
    }
}
