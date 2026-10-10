<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Mail — shared layout texts
    |--------------------------------------------------------------------------
    */

    'club_layout' => [
        'tagline' => 'Orienteering · club news',
        'footer_sender' => ':club · club message sent via :product',
        'help' => 'Help',
        'contact' => 'Contact the club',
        'notification_settings' => 'Notification settings',
        'quote_open' => '“',
        'quote_close' => '”',
        // Carbon isoFormat pattern for a day and month without the year
        'day_month_format' => 'MMMM D',
        'deadline' => 'Entries until :date',
        'terms' => [
            1 => 'First entry deadline',
            2 => 'Second entry deadline',
            3 => 'Third entry deadline',
        ],
        'term_ordinals' => [
            1 => 'first',
            2 => 'second',
            3 => 'third',
        ],
    ],

];
