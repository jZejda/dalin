<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity\EntryData;

class Data
{
    public Entry $Entry;

    public function __construct(Entry $Entry)
    {
        $this->Entry = $Entry;
    }

    public function getEntry(): Entry
    {
        return $this->Entry;
    }
}
