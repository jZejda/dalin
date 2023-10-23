<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Organization
{
    private ?string $Id;
    private string $Name;
    private ?string $ShortName;
    private string|array|null $Country;

    public function __construct(?string $Id, string $Name, ?string $ShortName, string|array|null $Country)
    {
        $this->Id = $Id;
        $this->Name = $Name;
        $this->ShortName = $ShortName;
        $this->Country = $Country;
    }

    public function getId(): ?string
    {
        return $this->Id;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getShortName(): ?string
    {
        return $this->ShortName;
    }

    public function getCountry(): string|array|null
    {
        return $this->Country;
    }
}
