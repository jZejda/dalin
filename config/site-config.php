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
        'full_name' => 'Klub orientačního běhu ALFA Brno z.s.',
        'primary_bank_account_number' => '159826453/0600',
        'primary_bank_account_name' => 'MONETA Money Bank, a. s.',
        'iban' => env('CLUB_IBAN', null),
        'user_credit_limit' => env('USER_CREDIT_LIMIT', -2000), //use integer
        'regular_membership_fees_prefix' => '111',
        'extra_membership_fees_prefix' => '888',
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
            'clubkey' => env('ORIS_GENERAL_CLUBKEY', null),
        ],
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
        ],
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
    | hours in format m ['08', '22']
    | days in month ['01', '05', '22']
    | months ['01', '12']
    | day in week ['0', '1', '5'] 0 = sunday, 1 = monday
    |
    */

    'cron_hourly' => [
        'url_key' => env('CRON_HOURLY_URL_KEY', 'url_key'),
        'weather_forecast' => [
            'active' => true,
            'hours' => ['08', '15'],
            'days_in_month' => ['*'],
            'months' => ['*'],
            'days_in_week' => ['*'],
        ],
        'event_update' => [
            'active' => true,
            'hours' => ['22', '04', '06'],
            'days_in_month' => ['*'],
            'months' => ['*'],
            'days_in_week' => ['*'],
        ],
        'bank_transaction_sync' => [
            'active' => true,
            'hours' => ['*'],
            'days_in_month' => ['*'],
            'months' => ['*'],
            'days_in_week' => ['*'],
        ],
        'mail_monthly_user_debit_report' => [
            'active' => true,
            'hours' => ['08'],
            'days_in_month' => ['01'],
            'months' => ['*'],
            'days_in_week' => ['*'],
        ],
        'mail_weekly_user_event_summary' => [
            'active' => true,
            'hours' => ['08'],
            'days_in_month' => ['*'],
            'months' => ['*'],
            'days_in_week' => ['0'],
        ],
        'mail_entry_ends_to_pay' => [
            'active' => true,
            'hours' => ['*'],
            'days_in_month' => ['*'],
            'months' => ['*'],
            'days_in_week' => ['*'],
        ],
    ],

    'cron_url_key' => env('CRON_URL_KEY', 'cron_url_key'),

];
