<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response;

class OrisUser
{
    private string $ID;
    private string $FirstName;
    private string $LastName;
    private ?string $RefLicenceOB;

    public function __construct(string $ID, string $FirstName, string $LastName, ?string $RefLicenceOB)
    {
        $this->ID = $ID;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->RefLicenceOB = $RefLicenceOB;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getFirstName(): string
    {
        return $this->FirstName;
    }

    public function getLastName(): string
    {
        return $this->LastName;
    }

    public function getRefLicenceOB(): ?string
    {
        return $this->RefLicenceOB;
    }
}
