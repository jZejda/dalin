<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Models\SportEventOrisStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * ORIS payment status of a few races with club entries, one of each kind: unpaid, a second-deadline
 * top-up with services, and paid races with published results. Demo races get fictitious ORIS IDs
 * (DEMO_ORIS_ID_FROM+) — OrisEventStatusRefresher never reloads them in demo mode.
 */
class DemoSportEventOrisStatusSeeder extends Seeder
{
    public const int DEMO_ORIS_ID_FROM = 990001;

    public function run(): void
    {
        $upcoming = $this->eventsWithEntries()
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->take(2)
            ->get();
        $finished = $this->eventsWithEntries()
            ->whereBetween('date', [Carbon::today()->subDays(60), Carbon::yesterday()])
            ->orderByDesc('date')
            ->take(2)
            ->get();

        $orisId = self::DEMO_ORIS_ID_FROM;
        $link = function (Collection $events) use (&$orisId): void {
            foreach ($events as $event) {
                /** @var SportEvent $event */
                $event->update(['oris_id' => $orisId++, 'last_update' => now()]);
            }
        };
        $link($upcoming);
        $link($finished);

        // Nearest race: first deadline is over, nothing paid yet
        if (($next = $upcoming->get(0)) !== null) {
            SportEventOrisStatus::factory()->create([
                'sport_event_id' => $next->id,
                'bank_account' => '2501234567/2010',
                'fee_entry' => 1680,
                'fee_si' => 80,
                'fee_service' => 0,
                'fee_total' => 1760,
                'paid' => 0,
                'to_be_paid' => 1760,
                'payment_vs' => '2690011',
            ]);
        }

        // Second race: first wave paid, late entries and accommodation added after it
        if (($second = $upcoming->get(1)) !== null) {
            SportEventOrisStatus::factory()->published()->create([
                'sport_event_id' => $second->id,
                'bank_account' => '19-2000145399/0800',
                'fee_entry' => 2340,
                'fee_si' => 0,
                'fee_service' => 1200,
                'fee_total' => 3540,
                'paid' => 2100,
                'to_be_paid' => 1440,
                'payment_vs' => '2690027',
            ]);
        }

        foreach ($finished as $event) {
            SportEventOrisStatus::factory()->paid()->published(results: true)->create([
                'sport_event_id' => $event->id,
                'bank_account' => '2800123456/2010',
            ]);
        }
    }

    /**
     * @return Builder<SportEvent>
     */
    private function eventsWithEntries(): Builder
    {
        return SportEvent::query()
            ->whereNull('oris_id')
            ->whereHas('userEntry', static fn (Builder $query): Builder => $query->whereIn('entry_status', EntryStatus::activeValues()));
    }
}
