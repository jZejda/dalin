<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset';

    protected $description = 'Reset the database and seed demo data (only works when DEMO_MODE=true)';

    public function handle(): int
    {
        if (config('demo.enabled') === false) {
            $this->error('Demo mode is disabled. Set DEMO_MODE=true to enable.');

            return Command::FAILURE;
        }

        $this->info('Starting demo database reset...');

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->info(Artisan::output());

        Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
        $this->info(Artisan::output());

        $this->info('Demo reset complete.');

        return Command::SUCCESS;
    }
}
