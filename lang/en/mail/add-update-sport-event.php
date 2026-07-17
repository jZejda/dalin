<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | AddUpdateSportEvent e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the sport event calendar changes e-mail.
    |
    */

    'subject' => [
        'add_update_sport_event' => 'Changes in the race calendar',
    ],

    'body' => [
        'heading'             => 'Changes in the race list',
        'intro'               => 'Selected information about changes in the :club entry system.',
        'events_intro'        => 'The following races have been added to the system:',
        'table_event'         => 'Race',
        'table_entry_until'   => 'Entry deadline',
        'divider_heading'     => 'Attempt',
        'unsubscribe_note'    => 'You can change your subscription to these messages directly in the client section settings.',
        'signoff'             => 'Take care and see you at the races - :club',
    ],

];
