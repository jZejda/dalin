<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserPasswordSend e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu se zasláním nebo resetem hesla k portálu.
    |
    */

    'subject' => [
        'send_password'  => 'Zaslání hesla k portálu',
        'reset_password' => 'Reset hesla k portálu',
    ],

    'body' => [
        'heading_new_account'  => 'Heslo do členské sekce - :club.',
        'heading_reset'        => 'Reset hesla do členské sekce - :club.',
        'intro_new_account'    => 'Byl vám vytvořen nový účet v přihláškovém systému oddílu :club.',
        'intro_reset'          => 'Bylo vám resetováno heslo v přihláškovém systému oddílu :club.',
        'address_label'        => 'Adresa',
        'name_label'            => 'Jméno',
        'login_label'           => 'Login',
        'password_label'        => 'Heslo',
        'help_heading'          => 'Nápověda / Řešení potíží',
        'help_text'             => 'Pro více informací, jak pracovat s aplikací, navštivte prosím naši nápovědu na [této stránce](:url).',
        'help_login_issues'     => 'V případě problémů s přihlášením, směřujte případné dotazy na email :email.',
        'help_password_change'  => 'Vygenerované heslo si lze po přihlášení v systému změnit.',
        'tips_heading'          => 'Mohlo by se hodit',
        'tips_text'             => 'Heslo si nejlépe ulož do některého důvěryhodného správce hesel jako například [KeePassXC](https://keepassxc.org/), [BitWarden](https://bitwarden.com/) a pod.',
    ],

];
