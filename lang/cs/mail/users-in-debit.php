<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UsersInDebit e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu s měsíčním výpisem uživatelů s nízkým kreditem.
    |
    */

    'subject' => [
        'users_in_debit' => 'Uživatelé s nízkým kreditem',
    ],

    'body' => [
        'heading'              => 'Uživatelé s nízkým kreditem',
        'intro'                => 'Měsíční výpis uživatelů :club klubu k dnešnímu dni :date, kteří k prvnímu mají nízký kredit na kontě.',
        'check_note'           => 'Prosím o kontrolu s následnou informací k uživatelům:',
        'table_header_user'    => 'Uživatel',
        'table_header_balance' => 'Stav konta Kč',
        'table_row_debit'      => ':debit Kč',
    ],

];
