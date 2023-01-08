<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;

final class DiscordWebhookHelper extends Controller
{
    public const DISCORD_SPORT_EVENT_WEBHOOK_URL = 'sport_event_notification';
    public const DISCORD_CONTENT_WEBHOOK_URL = 'content_notification';

    public static function getWebhookUrl(string $chanelName): string
    {
        return 'jedeme';
    }

}
