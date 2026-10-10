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

    'club' => [
        'eyebrow' => 'Pro správce · klubové finance',
        'title' => 'Přehled záporných zůstatků.',
        'lead' => 'Stav účtů členů ke dni :date. Před dalším postupem prosím zkontroluj aktuální zůstatky.',
        'section' => 'Členové s nízkým kreditem',
        'empty' => 'Žádný člen teď nemá záporný zůstatek.',
        'fine' => 'Částky odpovídají stavu v okamžiku vytvoření přehledu.',
    ],

];
