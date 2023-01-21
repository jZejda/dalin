<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity\EntryData;

use App\Http\Components\Oris\Response\Entity\EntryData\Entry;

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
