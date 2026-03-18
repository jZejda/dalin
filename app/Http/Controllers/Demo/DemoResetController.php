<?php

declare(strict_types=1);

namespace App\Http\Controllers\Demo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DemoResetController
{
    public function reset(): Response
    {
        if (config('demo.enabled') === false) {
            abort(404);
        }

        Log::channel('site')->info('Demo reset triggered via HTTP endpoint.');

        Artisan::call('demo:reset');

        return response('OK', 200);
    }
}
