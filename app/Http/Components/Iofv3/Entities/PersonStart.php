<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

class PersonStart
{
    private Person $Person;
    private Organization $Organisation;
    private Start $Start;

    public function __construct(Person $Person, Organization $Organisation, Start $Start)
    {
        $this->Person = $Person;
        $this->Organisation = $Organisation;
        $this->Start = $Start;
    }

    public function getPerson(): Person
    {
        return $this->Person;
    }

    public function getOrganisation(): Organization
    {
        return $this->Organisation;
    }

    public function getStart(): Start
    {
        return $this->Start;
    }
}
