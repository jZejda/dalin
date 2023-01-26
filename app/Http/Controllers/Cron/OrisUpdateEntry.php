<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Http\Components\Oris\GetEvent;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Http\Controllers\Controller;
use App\Models\SportEvent;
use App\Models\SportService;
use App\Services\OrisApiService;
use Illuminate\Support\Facades\Http;

class OrisUpdateEntry extends Controller
{

    public function update(int $orisEntryId): void
    {

        (new OrisApiService())->updateEvent($orisEntryId);

    }
}
