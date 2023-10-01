<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Person
{
    private string|array|null $Id; //BACHA
    private Name $Name;

    public function __construct(array|string|null $Id, Name $Name)
    {
        $this->Id = $Id;
        $this->Name = $Name;
    }

    public function getId(): array|string|null
    {
        return $this->Id;
    }

    public function getName(): Name
    {
        return $this->Name;
    }
}
