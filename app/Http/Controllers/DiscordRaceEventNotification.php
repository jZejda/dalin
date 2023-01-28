<?php

namespace App\Http\Controllers;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class DiscordRaceEventNotification extends Controller
{

    public string $content;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }


    public function notification(): PromiseInterface|Response
    {
        return Http::post('/', [
            'content' => $this->content,
            'embeds' => [
                [
                    'title' => "An awesome new notification!",
                    'description' => "Discord Webhooks are great!",
                    'color' => '7506394',
                ]
            ],
        ]);
    }
}
