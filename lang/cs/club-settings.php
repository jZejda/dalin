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
        'branding' => [
            'section' => 'Barvy klubu',
            'description' => 'Akcentní barva klubu v e-mailech (pruh v hlavičce, hlavní tlačítko, zvýraznění termínů). Barvu textu na tlačítku i světlé odstíny dopočítá aplikace sama.',
            'accent_color' => 'Akcentní barva',
            'accent_color_helper' => 'Barva ve formátu #rrggbb. Pokud ji nevyplníte, použije se výchozí :default. Logo a sociální sítě v e-mailech se přebírají ze sekce Veřejný web a sociální sítě.',
        ],
        'seo' => [
            'section' => 'Veřejný web a sociální sítě',
            'description' => 'Údaje pro vyhledávače (SEO) a náhledy odkazů sdílených na sociálních sítích.',
            'seo_description' => 'Popis klubu',
            'seo_description_helper' => 'Jedna až dvě věty o klubu (ideálně do 160 znaků). Zobrazuje se ve výsledcích vyhledávání a v náhledech odkazů u stránek bez vlastního popisu.',
            'seo_image' => 'Výchozí obrázek pro sdílení',
            'seo_image_helper' => 'JPG nebo PNG ve velikosti 1200 × 630 px (např. logo na barvách klubu). Použije se v náhledu odkazu na Facebooku, WhatsAppu apod. u stránek bez vlastního obrázku.',
            'seo_logo' => 'Logo klubu',
            'seo_logo_helper' => 'Čtvercové PNG nebo JPG, alespoň 112 × 112 px. Vyhledávače ho mohou zobrazit u výsledků klubu, zobrazuje se i v hlavičce e-mailů (doporučeno PNG).',
            'seo_same_as' => 'Profily na sociálních sítích',
            'seo_same_as_helper' => 'Celé adresy profilů klubu (Facebook, Instagram, Strava…). Každou potvrďte klávesou Enter.',
        ],
    ],

    'notification' => [
        'saved_title' => 'Nastavení klubu uloženo',
    ],

];
