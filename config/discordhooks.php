<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discord notification to the Chanel with webhooks
    |--------------------------------------------------------------------------
    |
    | This is the configuration of channels of Discord webhooks
    | Available for status false, true
    |
    */

    'discord_meta' => [
        'status'    => env('DISCORD_BOT_STATUS'),
        'bot_name'  => env('DISCORD_BOT_NAME'),
    ],

    'discord_hooks_url' => [
        'chanel1_url' => env('DISCORD_CHANEL1'),
    ],

];
