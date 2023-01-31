<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\OrisApiService;

class OrisUpdateEntry extends Controller
{
    public function update(int $orisEntryId): void
    {

        (new OrisApiService())->updateEvent($orisEntryId);

    }
}
