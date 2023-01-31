<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;
use App\Models\SportEvent;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class RaceEventAddedNotification extends Controller
{
    public SportEvent $sportEvent;
    public string $status;

    public function __construct(SportEvent $sportEvent, string $status = DiscordWebhookHelper::CONTENT_STATUS_NEW)
    {
        $this->sportEvent = $sportEvent;
        $this->status = $status;
    }

    public function sendNotification(): PromiseInterface|Response
    {

        $raceEventOrisLink = '';
        if (isset($this->sportEvent->oris_id)) {
            $raceEventOrisLink = sprintf('[%s](https://oris.orientacnisporty.cz/Zavod?id=%s)', $this->sportEvent->oris_id, $this->sportEvent->oris_id);
        }

        $embeds[] = [
            'title' => $this->sportEvent->name,
            'description' => sprintf('Přihláška do: %s | ORIS: %s', Carbon::parse($this->sportEvent->entry_date_1)->format('m.d.Y H:i'), $raceEventOrisLink),
            'color' => '5763719',
        ];

        $content = match ($this->status) {
            DiscordWebhookHelper::CONTENT_STATUS_NEW => 'Do systému jsme přidali **nový závod nebo akci**, to to prosím zkontroluj.',
            DiscordWebhookHelper::CONTENT_STATUS_UPDATE => 'Aktualizovali jsme údaje o **závodě / akci**. Pokud jí sleduješ, tak to prosím zkontroluj.',
            default => '',
        };

        return Http::post(DiscordWebhookHelper::getWebhookUrl(DiscordWebhookHelper::DISCORD_SPORT_EVENT_WEBHOOK_URL), [
            'content' => $content,
            'embeds' => $embeds,
        ]);
    }
}
