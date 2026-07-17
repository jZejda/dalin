<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EntryEndsToPay e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the entry deadline reminder e-mail.
    |
    */

    'subject' => [
        'entry_ends_to_pay' => 'Payment due for the :deadline entry deadline',
    ],

    'body' => [
        'heading'             => 'End of race entries',
        'intro'               => 'The **:deadline entry deadline** for the races listed below is ending now. Please pay the entry fees for the registered members.',
        'deadline_heading'    => ':deadline entry deadline',
        'deadline_text'       => 'Races for which the **:deadline entry deadline** is ending now.',
        'table_entry_until'   => 'Entry until',
        'table_event_name'    => 'Event/race name',
        'table_oris_id'       => 'ORIS ID',
    ],

];
