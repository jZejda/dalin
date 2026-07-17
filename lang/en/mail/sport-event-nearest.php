<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | SendSportEventNearestMail e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the nearest entry deadline e-mail.
    |
    */

    'subject' => [
        'nearest' => 'Upcoming entry deadline',
    ],

    'body' => [
        'heading'             => 'Entry deadline - 1st term',
        'intro'               => 'The entry deadline for the races listed below is approaching. Less than **two days** remain until the entry deadline. Please enter according to the instructions in the administration.',
        'table_entry_until'   => 'Entry deadline',
        'table_event_name'    => 'Race/event name',
        'signoff'             => 'Take care and see you at the races - :club',
        'unsubscribe_note'    => 'You can change your subscription to these messages directly in the client section settings.',
    ],

];
