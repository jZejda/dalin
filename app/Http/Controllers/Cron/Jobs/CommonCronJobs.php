<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

interface CommonCronJobs
{
    public function run(): void;
}
