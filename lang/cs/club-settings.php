<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Club Settings Page (Config cluster)
    |--------------------------------------------------------------------------
    |
    | Jazykové řetězce pro stránku Nastavení klubu.
    |
    */

    'navigation_label' => 'Nastavení klubu',
    'title' => 'Nastavení klubu',

    'form' => [
        'identification' => [
            'section' => 'Identifikace klubu',
            'abbr' => 'Zkratka klubu',
            'abbr_helper' => 'Zkratka se používá pro ORIS API a cestu k logu, proto ji lze změnit pouze v souboru config/site-config.php.',
            'full_name' => 'Celý název klubu',
        ],
        'bank_details' => [
            'section' => 'Bankovní údaje',
            'description' => 'Údaje se zobrazují členům v pokynech pro platbu kreditu.',
            'primary_bank_account_number' => 'Číslo hlavního účtu',
            'primary_bank_account_name' => 'Název banky',
            'iban' => 'IBAN',
            'iban_helper' => 'Používá se pro generování QR platby. Ponechte prázdné, pokud chcete použít hodnotu z konfiguračního souboru.',
        ],
        'credit' => [
            'section' => 'Kredit a členské příspěvky',
            'user_credit_limit' => 'Limit kreditu člena',
            'user_credit_limit_helper' => 'Záporné celé číslo. Nejnižší povolený zůstatek kreditu — po jeho dosažení se člen nemůže přihlásit na závod.',
            'regular_membership_fees_prefix' => 'Prefix VS řádných členských příspěvků',
            'regular_membership_fees_prefix_helper' => 'Pouze číslice.',
            'extra_membership_fees_prefix' => 'Prefix VS mimořádných příspěvků (dobití kreditu)',
            'extra_membership_fees_prefix_helper' => 'Pozor: používá se pro automatické párování bankovních transakcí. Platby zaslané se starým prefixem se po změně nespárují.',
        ],
        'contacts' => [
            'section' => 'Kontakty',
            'technical_email' => 'Technický e-mail',
            'technical_email_helper' => 'Kontakt uváděný v e-mailech členům (reset hesla, změny kreditu apod.).',
        ],
    ],

    'notification' => [
        'saved_title' => 'Nastavení klubu uloženo',
    ],

];
