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
    | Site Discord chanels
    |--------------------------------------------------------------------------
    |
    | Link to webhook Discord channels
    |
    */

    'discord' => [
        'sport_event' => [
            'webhook_url' => env('DISCORD_SPORT_EVENT_NOTIFICATION_WEBHOOK'),
            'code' => 'sport_event_notification',
        ],
        'content' => [
            'webhook_url' => env('DISCORD_CONTENT_NOTIFICATION_WEBHOOK'),
            'code' => 'content_notification',
        ]
    ]



];
