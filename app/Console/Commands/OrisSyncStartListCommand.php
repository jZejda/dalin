<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SportEvent;
use App\Services\OrisApiService;
use Illuminate\Console\Command;

class OrisSyncStartListCommand extends Command
{
    protected $signature = 'oris:sync-start-list {oris_id : ORIS ID race}';

    protected $description = 'Manually synchronizes the start list from the ORIS API for the specified ORIS race ID';

    public function handle(OrisApiService $orisApiService): int
    {
        $orisId = (int) $this->argument('oris_id');

        /** @var SportEvent|null $sportEvent */
        $sportEvent = SportEvent::query()->where('oris_id', $orisId)->first();

        if ($sportEvent === null) {
            $this->error("Race  ORIS ID {$orisId} was not found in the database.");
            return Command::FAILURE;
        }

        $this->info("I'm syncing the start list for: {$sportEvent->name} (ID: {$sportEvent->id}, ORIS ID: {$orisId})");

        $result = $orisApiService->updateStartList($sportEvent->id);

        if ($result) {
            $this->info('The start list has been successfully synchronized.');
            return Command::SUCCESS;
        }

        $this->error('Synchronization failed — the ORIS API did not return a valid response (the race sheet may not have been published yet).');
        return Command::FAILURE;
    }
}
