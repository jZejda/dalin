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

    'club' => [
        'eyebrow' => 'Club account · new movement',
        'title_credit' => ':amount was added to your account.',
        'title_debit' => ':amount was deducted from your account.',
        'lead' => 'We recorded a new movement on your club account.',
        'balance_label' => 'Current balance',
        'balance_note' => 'As of :date',
        'amount_label' => 'Amount',
        'date_label' => 'Date',
        'transaction_id_label' => 'Transaction ID',
        'bank_transaction_id_label' => 'Bank transaction ID',
        'fine' => 'If you have questions about this movement, contact the club: :email.',
        'fine_no_contact' => 'If you have questions about this movement, contact the club.',
    ],

];
