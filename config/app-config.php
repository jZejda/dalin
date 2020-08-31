<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Config
    |--------------------------------------------------------------------------
    |
    | This option controls the default pages settings.
    |
    */

    /*
     * URL to default Wiki Page
     */
    'default_help_url' => [
        'url' => 'https://oplan.cz/',
    ],

    'content_mode' => [
        '1' => 'html',
        '2' => 'markdown',
    ],

    'mail_contact' => [
        'admin' => 'admin@abmbrno.cz',
    ],

    'forecast_api' => [
        'url' => 'https://api.darksky.net/forecast',
        'token' => env('FORECAST_TOKEN')
    ],


    'custom_organizer' => [
        '1' => 'html',
        '2' => 'markdown',
    ],

    'result_type' => [
        '1' => 'xml_iof_v3_file',
        '2' => 'html_iframe'
    ]

];
