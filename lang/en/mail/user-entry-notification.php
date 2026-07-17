<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserEntryNotification e-mail
    |--------------------------------------------------------------------------
    |
    | Body of the message e-mail sent to registered racers. The subject
    | contains dynamic user-provided text and is not localized.
    |
    */

    'body' => [
        'heading'    => 'Race notification',
        'intro'      => 'Message about the race **:name**:alt_name sent to all currently registered racers.',
        'date_line'  => 'Date: **:date**',
        'place_line' => 'Location: **:place**',
    ],

];
