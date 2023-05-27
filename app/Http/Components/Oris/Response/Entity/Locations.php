<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

final class Locations
{
    private string $ID;
    private string $GPSLat;
    private string $GPSLon;
    private ?string $NameCZ;
    private ?string $NameEN;
    private ?string $Letter;

    public function __construct(string $ID, string $GPSLat, string $GPSLon, ?string $NameCZ, ?string $NameEN, ?string $Letter)
    {
        $this->ID = $ID;
        $this->GPSLat = $GPSLat;
        $this->GPSLon = $GPSLon;
        $this->NameCZ = $NameCZ;
        $this->NameEN = $NameEN;
        $this->Letter = $Letter;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getGPSLat(): string
    {
        return $this->GPSLat;
    }

    public function getGPSLon(): string
    {
        return $this->GPSLon;
    }

    public function getNameCZ(): ?string
    {
        return $this->NameCZ;
    }

    public function getNameEN(): ?string
    {
        return $this->NameEN;
    }

    public function getLetter(): ?string
    {
        return $this->Letter;
    }
}
