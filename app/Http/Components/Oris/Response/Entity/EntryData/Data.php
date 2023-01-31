<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity\EntryData;

class Data
{
    public Entry $entry;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function getEntry(): Entry
    {
        return $this->entry;
    }
}
