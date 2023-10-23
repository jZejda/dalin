<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class SportClass
{
    private ?string $Id;
    private string $Name;
    private ?string $ShortName;

    /**
     * @param string|null $Id
     * @param string $Name
     * @param string $ShortName
     */
    public function __construct(?string $Id, string $Name, ?string $ShortName)
    {
        $this->Id = $Id;
        $this->Name = $Name;
        $this->ShortName = $ShortName;
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
}
