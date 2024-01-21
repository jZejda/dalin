<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

class Services
{
    public int $ID;
    public string $NameCZ;
    public ?string $NameEN;
    public ?string $LastBookingDateTime;
    public ?string $UnitPrice;
    public ?string $QtyAvailable;
    public ?string $QtyRemaining;

    public function __construct(int $ID, string $NameCZ, ?string $NameEN, ?string $LastBookingDateTime, ?string $UnitPrice, ?string $QtyAvailable, ?string $QtyRemaining)
    {
        $this->ID = $ID;
        $this->NameCZ = $NameCZ;
        $this->NameEN = $NameEN;
        $this->LastBookingDateTime = $LastBookingDateTime;
        $this->UnitPrice = $UnitPrice;
        $this->QtyAvailable = $QtyAvailable;
        $this->QtyRemaining = $QtyRemaining;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function getNameCZ(): string
    {
        return $this->NameCZ;
    }

    public function getNameEN(): ?string
    {
        return $this->NameEN;
    }

    public function getLastBookingDateTime(): ?string
    {
        return $this->LastBookingDateTime;
    }

    public function getUnitPrice(): ?string
    {
        return $this->UnitPrice;
    }

    public function getQtyAvailable(): ?string
    {
        return $this->QtyAvailable;
    }

    public function getQtyRemaining(): ?string
    {
        return $this->QtyRemaining;
    }
}
