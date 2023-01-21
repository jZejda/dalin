<?php

namespace App\Http\Components\Oris\Response\Entity\EntryData;

class Entry
{
    public string $ID;

    public function __construct(string $ID)
    {
        $this->ID = $ID;
    }

    public function getID(): string
    {
        return $this->ID;
    }
}
