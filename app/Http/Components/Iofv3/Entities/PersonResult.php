<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

use App\Http\Components\Iofv3\Entities\Organization;

class PersonResult
{
    private Person $Person;
    private Organization $Organisation;
    private Result $Result;

    public function __construct(Person $Person, \App\Http\Components\Iofv3\Entities\Organization $Organisation, Result $Result)
    {
        $this->Person = $Person;
        $this->Organisation = $Organisation;
        $this->Result = $Result;
    }

    public function getPerson(): Person
    {
        return $this->Person;
    }

    public function getOrganisation(): \App\Http\Components\Iofv3\Entities\Organization
    {
        return $this->Organisation;
    }

    public function getResult(): Result
    {
        return $this->Result;
    }
}
