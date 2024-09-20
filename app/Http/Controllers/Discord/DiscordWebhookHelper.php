<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;

final class DiscordWebhookHelper extends Controller
{
    public const string DISCORD_SPORT_EVENT_WEBHOOK_URL = 'sport_event_notification';
    public const string DISCORD_CONTENT_WEBHOOK_URL = 'content_notification';

    public const string CONTENT_STATUS_NEW = 'new';
    public const string CONTENT_STATUS_UPDATE = 'update';


    public static function getWebhookUrl(string $chanelName): string
    {

        return match ($chanelName) {
            self::DISCORD_SPORT_EVENT_WEBHOOK_URL => config('site-config.discord.sport_event.webhook_url'),
            self::DISCORD_CONTENT_WEBHOOK_URL => config('site-config.discord.content.webhook_url'),
            default => '',
        };
    }
}
