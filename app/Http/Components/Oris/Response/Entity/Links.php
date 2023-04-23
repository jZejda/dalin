<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

use App\Http\Components\Oris\Response\Entity\SourceType;

final class Links
{
    public string $ID;
    public string $Url;
    public ?SourceType $SourceType;
    public string|null $OtherDescCZ;
    public string|null $OtherDescEN;

    public function __construct (string $ID, string $Url, ?SourceType $SourceType, ?string $OtherDescCZ, ?string $OtherDescEN)
    {
        $this->ID = $ID;
        $this->Url = $Url;
        $this->SourceType = $SourceType;
        $this->OtherDescCZ = $OtherDescCZ;
        $this->OtherDescEN = $OtherDescEN;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getUrl(): string
    {
        return $this->Url;
    }

    public function getSourceType(): ?SourceType
    {
        return $this->SourceType;
    }

    public function getOtherDescCZ(): ?string
    {
        return $this->OtherDescCZ;
    }

    public function getOtherDescEN(): ?string
    {
        return $this->OtherDescEN;
    }
}