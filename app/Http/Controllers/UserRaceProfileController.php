<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsersRaceProfileExport;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserRaceProfileController
{
    public function export(string $exportType): Response|BinaryFileResponse
    {
        return (new UsersRaceProfileExport())->forUserRaceProfile($exportType)
            ->download('registrace' . '-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }
}
