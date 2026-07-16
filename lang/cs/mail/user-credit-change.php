<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserCreditChange e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu o pohybu na uživatelském kontě.
    |
    */

    'subject' => [
        'userCreditChange' => 'Pohyb na účtu uživatele',
    ],

    'body' => [
        'heading'                  => 'Pohyb na účtu',
        'intro'                    => 'Na účtu uživatele **:name** došlo k pohybu.',
        'balance'                  => 'Aktuální výše uživatelského konta: **:balance** Kč k datu **:date**.',
        'last_transaction_heading' => 'Pohyb z poslední transakce',
        'amount'                   => 'částka: **:amount Kč**',
        'transaction_date'         => 'datum transakce: :date',
        'transaction_id'           => 'ID transakce: :id',
        'bank_transaction_id'      => 'vazba na ID bankovní transakce: :id',
        'contact'                  => 'Pokud by byla v transakci nějaká nesrovnalost, prosím kontaktujte nás na e-mailu: :email',
        'signoff'                  => 'Mějte se fajn a jezděte na závody - :club',
    ],

];
