<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3;

use App\Http\Components\Iofv3\Entities\ClassStart;
use App\Http\Components\Iofv3\Entities\Event;

final class StartList
{
    private Event $Event;
    /** @var ClassStart[] $ClassStart */
    private array $ClassStart;

    /**
     * @param Event $Event
     * @param ClassStart[] $ClassStart
     */
    public function __construct(Event $Event, array $ClassStart)
    {
        $this->Event = $Event;
        $this->ClassStart = $ClassStart;
    }

    public function getEvent(): Event
    {
        return $this->Event;
    }

    public function getClassStart(): array
    {
        return $this->ClassStart;
    }

}
