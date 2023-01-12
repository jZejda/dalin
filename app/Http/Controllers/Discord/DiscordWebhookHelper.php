<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;

final class DiscordWebhookHelper extends Controller
{
    public const DISCORD_SPORT_EVENT_WEBHOOK_URL = 'sport_event_notification';
    public const DISCORD_CONTENT_WEBHOOK_URL = 'content_notification';

    public const CONTENT_STATUS_NEW = 'new';
    public const CONTENT_STATUS_UPDATE = 'update';


    public static function getWebhookUrl(string $chanelName): string
    {

        return match ($chanelName) {
            self::DISCORD_SPORT_EVENT_WEBHOOK_URL => config('site-config.discord.sport_event.webhook_url'),
            self::DISCORD_CONTENT_WEBHOOK_URL => config('site-config.discord.content.webhook_url'),
            default => '',
        };
    }
}