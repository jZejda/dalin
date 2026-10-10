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

    'club' => [
        'eyebrow' => 'Entries · deadline approaching',
        'title' => 'You can still make the first deadline.',
        'lead' => '{1} The first entry deadline for the following races ends in :count day.|[2,*] The first entry deadline for the following races ends in :count days.',
        'action' => 'Choose a race and enter',
    ],

];
