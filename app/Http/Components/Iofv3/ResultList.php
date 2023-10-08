<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3;

use App\Http\Components\Iofv3\Entities\ClassResult;
use App\Http\Components\Iofv3\Entities\Event;

final class ResultList
{
    private Event $Event;
    /** @var ClassResult[] $ClassResult */
    private ?array $ClassResult;

    public function __construct(Event $Event, ?array $ClassResult)
    {
        $this->Event = $Event;
        $this->ClassResult = $ClassResult;
    }

    public function getEvent(): Event
    {
        return $this->Event;
    }

    public function getClassResult(): ?array
    {
        return $this->ClassResult;
    }
}
