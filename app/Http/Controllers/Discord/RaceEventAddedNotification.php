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

final class RaceEventAddedNotification extends Controller
{

    public SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function notification(): PromiseInterface|Response
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


        return Http::post(env('DISCORD_MAIN_NOTIFICATION_WEBHOOK', 'empty'), [
            'content' => 'Nově přidaný závod akce **tak na ní koukni**.',
            'embeds' => $embeds,
        ]);
    }
}
