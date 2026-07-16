<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BankAccount Resource
    |--------------------------------------------------------------------------
    |
    | Překlady pro správu bankovních napojení (BankAccountResource).
    |
    */

    'navigation_label' => 'Bankovní napojení',
    'label'            => 'Bankovní napojení',
    'plural_label'     => 'Bankovní napojení',

    'form' => [
        'section_connection'      => 'Napojení',
        'name'                    => 'Název',
        'code'                    => 'Banka (konektor)',
        'code_helper'             => 'Konektor nelze u existujícího napojení měnit — odeber napojení a přidej nové.',
        'currency'                => 'Měna',
        'active'                  => 'Napojení je aktivní',
        'active_helper'           => 'Transakce se stahují jen z aktivních napojení.',
        'section_credentials'     => 'Přístupové údaje',
        'credentials_description' => 'Údaje se ukládají šifrovaně a zpětně se nezobrazují. Při editaci ponech pole prázdné, pokud chceš zachovat uloženou hodnotu.',
        'credential_keep_helper'  => 'Ponech prázdné pro zachování uložené hodnoty.',
    ],

    'table' => [
        'name'         => 'Název',
        'bank'         => 'Banka',
        'currency'     => 'Měna',
        'last_synced'  => 'Poslední synchronizace',
        'never'        => 'nikdy',
        'active'       => 'Aktivní',
    ],

    'credentials' => [
        'token'      => 'API token',
        'account_id' => 'ID účtu (account_id)',
    ],

];
