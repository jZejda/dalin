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
            $this->error("Závod s ORIS ID {$orisId} nebyl nalezen v databázi.");
            return Command::FAILURE;
        }

        $this->info("Synchronizuji startovní listinu: {$sportEvent->name} (ID: {$sportEvent->id}, ORIS ID: {$orisId})");

        $result = $orisApiService->updateStartList($sportEvent->id);

        if ($result) {
            $this->info('Startovní listina byla úspěšně synchronizována.');
            return Command::SUCCESS;
        }

        $this->error('Synchronizace selhala — ORIS API nevrátilo platnou odpověď (startovní listina zatím nemusí být zveřejněna).');
        return Command::FAILURE;
    }
}
