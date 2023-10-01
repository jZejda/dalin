<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

use App\Http\Components\Iofv3\Entities\Organization;

class PersonStart
{
    private Person $Person;
    private Organization $Organisation;
    private Start $Start;

    public function __construct(Person $Person, \App\Http\Components\Iofv3\Entities\Organization $Organisation, Start $Start)
    {
        $this->Person = $Person;
        $this->Organisation = $Organisation;
        $this->Start = $Start;
    }

    public function getPerson(): Person
    {
        return $this->Person;
    }

    public function getOrganisation(): \App\Http\Components\Iofv3\Entities\Organization
    {
        return $this->Organisation;
    }

    public function getStart(): Start
    {
        return $this->Start;
    }
}
