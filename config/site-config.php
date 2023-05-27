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
    | Site OpenMapForecast
    |--------------------------------------------------------------------------
    |
    | Config OpenMapForecast
    |
    */

    'open_map_forecast_api_key' => env('OPEN_MAP_API_KEY', 'open_map_forecast_api_key'),

    /*
    |--------------------------------------------------------------------------
    | Site Cron Hourly settings
    |--------------------------------------------------------------------------
    |
    | Config for hourly cron runners
    |
    */

    'cron_hourly' => [
        'url_key' => env('CRON_HOURLY_URL_KEY', 'url_key'),
        'weather_forecast' => [
            'active' => true,
            'hours' => ['08', '15'],
        ],
        'event_update' => [
            'active' => true,
            'hours' => ['22'],
        ],
    ],

    'cron_url_key' => env('CRON_URL_KEY', 'cron_url_key'),

];
