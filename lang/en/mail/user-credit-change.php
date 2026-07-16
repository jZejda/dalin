<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserCreditChange e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the user credit movement e-mail.
    |
    */

    'subject' => [
        'userCreditChange' => 'Movement on the user account',
    ],

    'body' => [
        'heading'                  => 'Account movement',
        'intro'                    => 'There has been a movement on the account of **:name**.',
        'balance'                  => 'Current account balance: **:balance** CZK as of **:date**.',
        'last_transaction_heading' => 'Latest transaction',
        'amount'                   => 'amount: **:amount CZK**',
        'transaction_date'         => 'transaction date: :date',
        'transaction_id'           => 'transaction ID: :id',
        'bank_transaction_id'      => 'linked bank transaction ID: :id',
        'contact'                  => 'If there is any discrepancy in the transaction, please contact us at: :email',
        'signoff'                  => 'Take care and see you at the races - :club',
    ],

];
