<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries\Strategy;

use App\Models\SportEvent;
use App\Services\SportEvents\Entries\EntryResult;

interface EntryStrategy
{
    public function canHandle(SportEvent $event): bool;

    /** @param array<string, mixed> $data */
    public function create(SportEvent $event, array $data): EntryResult;
}
