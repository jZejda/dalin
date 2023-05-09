<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site Club info
    |--------------------------------------------------------------------------
    |
    | Basic Club info
    |
    */

    'club' => [
        'abbr' => 'ABM',
        'full_name' => 'Klub orientačního běhu ABM Brno'
    ],

    /*
    |--------------------------------------------------------------------------
    | ORIS credentials
    |--------------------------------------------------------------------------
    |
    | Basic Club info
    |
    */

    'oris_credentials' => [
        'general' => [
            'username' => env('ORIS_GENERAL_USERNAME', null),
            'password' => env('ORIS_GENERAL_PASSWORD', null),
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | Site Discord channels
    |--------------------------------------------------------------------------
    |
    | Link to webhook Discord channels
    |
    */

    'discord' => [
        'sport_event' => [
            'webhook_url' => env('DISCORD_SPORT_EVENT_NOTIFICATION_WEBHOOK', ''),
            'code' => 'sport_event_notification',
        ],
        'content' => [
            'webhook_url' => env('DISCORD_CONTENT_NOTIFICATION_WEBHOOK', ''),
            'code' => 'content_notification',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Site Cron settings
    |--------------------------------------------------------------------------
    |
    | Config for hourly cron runs
    |
    */

    'cron_hourly' => [
        'weather_forecast' => [
            'active' => true,
            'hours' => ['08', '15'],
        ],
        'event_update' => [
            'active' => true,
            'hours' => ['00'],
        ],
    ],



];
