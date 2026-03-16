<?php

namespace App\Http\Controllers\Discord;

use App\Shared\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class RaceEventAddedNotification extends Controller
{
    public SportEvent $sportEvent;
    public string $status;

    public function __construct(SportEvent $sportEvent, string $status = DiscordWebhookHelper::CONTENT_STATUS_NEW)
    {
        $this->sportEvent = $sportEvent;
        $this->status = $status;
    }

    public function sendNotification(): PromiseInterface|Response|null
    {

        $raceEventOrisLink = '';
        if (isset($this->sportEvent->oris_id)) {
            $raceEventOrisLink = sprintf('[%s](%s/Zavod?id=%s)', $this->sportEvent->oris_id, OrisApiService::ORIS_URL, $this->sportEvent->oris_id);
        }

        $embeds[] = [
            'title' => $this->sportEvent->name,
            'description' => sprintf('Přihláška do: %s | ORIS: %s', Carbon::parse($this->sportEvent->entry_date_1)->format(AppHelper::DATE_TIME_FORMAT), $raceEventOrisLink),
            'color' => '5763719',
        ];

        $content = match ($this->status) {
            DiscordWebhookHelper::CONTENT_STATUS_NEW => 'Do systému jsme přidali **nový závod nebo akci**, to to prosím zkontroluj.',
            DiscordWebhookHelper::CONTENT_STATUS_UPDATE => 'Aktualizovali jsme údaje o **závodě / akci**. Pokud jí sleduješ, tak to prosím zkontroluj.',
            default => '',
        };

        $url = DiscordWebhookHelper::getWebhookUrl(DiscordWebhookHelper::DISCORD_SPORT_EVENT_WEBHOOK_URL);

        if ($url === null) {
            Log::channel('site')->warning('RaceEventAddedNotification: Discord webhook URL is not configured, skipping.');
            return null;
        }

        return Http::post($url, [
            'content' => $content,
            'embeds' => $embeds,
        ]);
    }
}
