<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UsersInDebit e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the monthly statement of low-credit users.
    |
    */

    'subject' => [
        'users_in_debit' => 'Users with low credit',
    ],

    'body' => [
        'heading'              => 'Users with low credit',
        'intro'                => 'Monthly statement of :club club users as of today, :date, who have a low credit balance at the start of the month.',
        'check_note'           => 'Please review and follow up with the users below:',
        'table_header_user'    => 'User',
        'table_header_balance' => 'Account balance CZK',
        'table_row_debit'      => ':debit CZK',
    ],

];
