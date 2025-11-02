<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class PersonEntry
{
    private Person $Person;
    private ?Organization $Organisation;
    private ?string $ControlCard;
    private ClassEntry $Class;
    private string $EntryTime;

    /**
     * @param Person $Person
     * @param ?Organization $Organisation
     * @param ?string $ControlCard
     * @param ClassEntry $Class
     * @param string $EntryTime
     */
    public function __construct(
        Person $Person,
        ?Organization $Organisation,
        ?string $ControlCard,
        ClassEntry $Class,
        string $EntryTime
    ) {
        $this->Person = $Person;
        $this->Organisation = $Organisation;
        $this->ControlCard = $ControlCard;
        $this->Class = $Class;
        $this->EntryTime = $EntryTime;
    }

    public function getPerson(): Person
    {
        return $this->Person;
    }

    public function getOrganisation(): ?Organization
    {
        return $this->Organisation;
    }

    public function getControlCard(): ?string
    {
        return $this->ControlCard;
    }

    public function getClass(): ClassEntry
    {
        return $this->Class;
    }

    public function getEntryTime(): string
    {
        return $this->EntryTime;
    }
}
