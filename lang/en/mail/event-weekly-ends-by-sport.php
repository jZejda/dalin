<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EventWeeklyEndsBySport e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the weekly summary of race entry deadlines e-mail.
    |
    */

    'subject' => [
        'event_weekly_ends_by_sport' => 'Weekly summary of entry deadlines',
    ],

    'body' => [
        'heading'              => 'Entry deadlines',
        'intro'                => 'Weekly summary of entries for the races listed below. Races are grouped by their entry deadline term for the week of **:from** - **:to**.',
        'first_term_heading'   => '1st entry term',
        'first_term_intro'     => 'Races for which the **first term** of entries is ending.',
        'second_term_heading'  => '2nd entry term',
        'second_term_intro'    => 'Races for which the **second term** of entries is ending.',
        'third_term_heading'   => '3rd entry term',
        'third_term_intro'     => 'Races for which the **third term** of entries is ending.',
        'table_entry_until'    => 'Entry deadline',
        'table_event_date'     => 'Race date',
        'table_event_name'     => 'Race/event name',
    ],

];
