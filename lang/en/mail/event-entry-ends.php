<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EventEntryEnds e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the upcoming race entry deadline e-mail.
    |
    */

    'subject' => [
        'event_entry_ends' => 'Race entry deadline approaching',
    ],

    'body' => [
        'heading'             => 'Entry deadline - 1st term',
        'intro'               => 'The entry deadline for the races listed below is approaching. Less than **:days days** remain until the entry deadline. Please enter according to the instructions in the administration.',
        'table_entry_until'   => 'Entry deadline',
        'table_event_name'    => 'Race/event name',
        'table_oris_id'       => 'ORIS ID',
    ],

];
