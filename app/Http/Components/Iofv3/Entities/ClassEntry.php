<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class ClassEntry
{
    private ?string $Id;
    private string $Name;
    private string $ShortName;

    public function __construct(?string $Id, string $Name)
    {
        $this->Id = $Id;
        $this->Name = $Name;
        $this->ShortName = $Name; // ShortName je stejný jako Name
    }

    public function getId(): ?string
    {
        return $this->Id;
    }

    public function getName(): string
    {
        return $this->Name;
    }

    public function getShortName(): string
    {
        return $this->ShortName;
    }
}
