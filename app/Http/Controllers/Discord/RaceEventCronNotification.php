<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;
use App\Models\SportEvent;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

final class RaceEventCronNotification extends Controller
{
    /**
     * Run one per day
     */

    public function notification(): PromiseInterface|Response
    {
        $raceEventsToNotify = $this->getRaceEventData();

        $embeds = [];

        if($raceEventsToNotify->isNotEmpty()) {

            /** @var SportEvent $raceEvent */
            foreach ($raceEventsToNotify as $raceEvent) {

                $raceEventOrisLink = '';
                if (isset($raceEvent->oris_id)) {
                    $raceEventOrisLink = sprintf('[%s](https://oris.orientacnisporty.cz/Zavod?id=%s)', $raceEvent->oris_id, $raceEvent->oris_id);
                }

                $embeds[] = [
                    'title' => $raceEvent->name,
                    'description' => sprintf('Přihláška do: %s | ORIS: %s', Carbon::parse($raceEvent->entry_date_1)->format('m.d.Y H:i'), $raceEventOrisLink),
                    'color' => '7506394',
                ];
            }
        }

        return Http::post(env('DISCORD_MAIN_NOTIFICATION_WEBHOOK', 'empty'), [
            'content' => 'Závody u kterých se blíží termín přihlášek na **první termín**.',
            'embeds' => $embeds,
        ]);
    }

    private function getRaceEventData(): Collection
    {
        return DB::table('sport_events')
            ->whereBetween('entry_date_1', [Carbon::now()->addDay(), Carbon::now()->addDays(2)])
            ->get();
    }
}
