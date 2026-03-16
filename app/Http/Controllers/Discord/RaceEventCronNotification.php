<?php

namespace App\Http\Controllers\Discord;

use App\Shared\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class RaceEventCronNotification extends Controller
{
    /**
     * Run one per day
     */
    public function notification(): PromiseInterface|Response|null
    {
        $raceEventsToNotify = $this->getRaceEventData();

        $embeds = [];

        if ($raceEventsToNotify->isNotEmpty()) {

            /** @var SportEvent $raceEvent */
            foreach ($raceEventsToNotify as $raceEvent) {

                $raceEventOrisLink = '';
                if (isset($raceEvent->oris_id)) {
                    $raceEventOrisLink = sprintf('[%s](%s/Zavod?id=%s)', $raceEvent->oris_id, OrisApiService::ORIS_URL, $raceEvent->oris_id);
                }

                $embeds[] = [
                    'title' => $raceEvent->name,
                    'description' => sprintf('Přihláška do: %s | ORIS: %s', Carbon::parse($raceEvent->entry_date_1)->format(AppHelper::DATE_TIME_FORMAT), $raceEventOrisLink),
                    'color' => '7506394',
                ];
            }
        }

        $url = DiscordWebhookHelper::getWebhookUrl(DiscordWebhookHelper::DISCORD_CONTENT_WEBHOOK_URL);

        if ($url === null) {
            Log::channel('site')->warning('RaceEventCronNotification: Discord webhook URL is not configured, skipping.');
            return null;
        }

        return Http::post($url, [
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
