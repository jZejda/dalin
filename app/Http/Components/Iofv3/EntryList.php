<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3;

use App\Http\Components\Iofv3\Entities\EventEntry;
use App\Http\Components\Iofv3\Entities\PersonEntry;

final class EntryList
{
    private EventEntry $Event;
    /** @var PersonEntry[] $PersonEntry */
    private array $PersonEntry;

    /**
     * @param EventEntry $Event
     * @param PersonEntry[] $PersonEntry
     */
    public function __construct(EventEntry $Event, array $PersonEntry)
    {
        $this->Event = $Event;
        $this->PersonEntry = $PersonEntry;
    }

    public function getEvent(): EventEntry
    {
        return $this->Event;
    }

    /**
     * @return PersonEntry[]
     */
    public function getPersonEntry(): array
    {
        return $this->PersonEntry;
    }
}
