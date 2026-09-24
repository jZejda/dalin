<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Help;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

#[Signature('oris:sync-disciplines
    {--dry-run : Only report the differences, change nothing}
    {--discipline=* : Limit to events with this local discipline_id (repeatable)}
    {--sleep=200 : Pause between ORIS requests in milliseconds}')]
#[Description('Re-reads the discipline of every ORIS event from the ORIS API and fixes a stale local discipline_id')]
#[Help('ORIS renumbered its disciplines; events imported before that keep the old numbers (e.g. 14 = sprint relay, 15 = knock-out). '
    .'Run with --dry-run first. Upcoming events that become a relay discipline get the default relay team.')]
class OrisSyncDisciplinesCommand extends Command
{
    public function handle(OrisApiService $orisApiService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $sleepMicros = max(0, (int) $this->option('sleep')) * 1000;

        /** @var array<int, string> $disciplineFilter */
        $disciplineFilter = (array) $this->option('discipline');

        $disciplines = SportDiscipline::query()->get()->keyBy('id');

        $events = SportEvent::query()
            ->whereNotNull('oris_id')
            ->when($disciplineFilter !== [], fn ($query) => $query->whereIn('discipline_id', array_map('intval', $disciplineFilter)))
            ->orderBy('date')
            ->get();

        if ($events->isEmpty()) {
            $this->info('No ORIS events to check.');

            return self::SUCCESS;
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '').'Checking '.$events->count().' ORIS events…');

        $rows = [];
        $failed = 0;
        $today = Carbon::today();

        $this->withProgressBar($events, function (SportEvent $event) use (
            $orisApiService,
            $disciplines,
            $dryRun,
            $sleepMicros,
            $today,
            &$rows,
            &$failed
        ): void {
            $orisDisciplineId = $this->fetchDisciplineId($orisApiService, (int) $event->oris_id, $sleepMicros);

            if ($orisDisciplineId === null) {
                $failed++;
                $rows[] = $this->row($event, $disciplines, $event->discipline_id, null, 'ORIS error — skipped');

                return;
            }

            if ($orisDisciplineId === $event->discipline_id) {
                return;
            }

            if (! $disciplines->has($orisDisciplineId)) {
                $failed++;
                $rows[] = $this->row($event, $disciplines, $event->discipline_id, $orisDisciplineId, 'unknown discipline, run SportDisciplinesSeeder — skipped');

                return;
            }

            $wasRelay = $event->isRelayDiscipline();
            $oldDisciplineId = $event->discipline_id;
            $notes = [];

            if (! $dryRun) {
                $event->discipline_id = $orisDisciplineId;
                $event->save();
                $event->unsetRelation('sportDiscipline');
            }

            $isRelay = $disciplines->get($orisDisciplineId)?->isRelayDiscipline() ?? false;

            if ($isRelay && ! $wasRelay) {
                if ($event->relayTeams()->exists()) {
                    $notes[] = 'now relay';
                } elseif ($event->date !== null && $event->date->lt($today)) {
                    $notes[] = 'now relay (past event, no team created)';
                } else {
                    $notes[] = $dryRun ? 'now relay, default team would be created' : 'now relay, default team created';

                    if (! $dryRun) {
                        $event->ensureDefaultRelayTeam();
                    }
                }
            }

            if ($wasRelay && ! $isRelay) {
                $teams = $event->relayTeams()->count();
                $notes[] = 'no longer relay'.($teams > 0 ? " — check {$teams} relay team(s)" : '');
            }

            $rows[] = $this->row($event, $disciplines, $oldDisciplineId, $orisDisciplineId, implode('; ', $notes));
        });

        $this->newLine(2);

        if ($rows === []) {
            $this->info('All disciplines match ORIS.');

            return self::SUCCESS;
        }

        $this->table(['ID', 'ORIS ID', 'Date', 'Event', 'Local', 'ORIS', 'Note'], $rows);

        $changed = count($rows) - $failed;
        $this->info(($dryRun ? 'Would change' : 'Changed').": {$changed}, skipped: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * ORIS occasionally times out — one retry keeps a long run from ending with random skips.
     */
    private function fetchDisciplineId(OrisApiService $orisApiService, int $orisId, int $sleepMicros): ?int
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                return $orisApiService->getEventDisciplineId($orisId);
            } catch (Throwable $e) {
                if ($attempt === 2) {
                    report($e);
                }
            } finally {
                usleep($sleepMicros);
            }
        }

        return null;
    }

    /**
     * @param Collection<int, SportDiscipline> $disciplines
     * @return array<int, string>
     */
    private function row(SportEvent $event, Collection $disciplines, ?int $oldId, ?int $newId, string $note): array
    {
        $label = static fn (?int $id): string => $id === null
            ? '—'
            : $id.' '.($disciplines->get($id)->short_name ?? '?');

        return [
            (string) $event->id,
            (string) $event->oris_id,
            $event->date?->format('Y-m-d') ?? '',
            mb_strimwidth($event->name, 0, 45, '…'),
            $label($oldId),
            $label($newId),
            $note,
        ];
    }
}
