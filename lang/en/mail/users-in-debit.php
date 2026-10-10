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

    'club' => [
        'eyebrow' => 'For administrators · club finances',
        'title' => 'Negative balances overview.',
        'lead' => 'Member account balances as of :date. Please check the current balances before taking further action.',
        'section' => 'Members with low credit',
        'empty' => 'No member has a negative balance right now.',
        'fine' => 'The amounts reflect the balances at the moment this overview was created.',
    ],

];
