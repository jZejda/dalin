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

    'club' => [
        'name_label' => 'Jméno',
        'email_label' => 'Přihlašovací e-mail',
        'password_label' => 'Vygenerované heslo',
        'url_label' => 'Členská sekce',
        'action' => 'Přihlásit se do DaLinu',

        'new_account' => [
            'eyebrow' => 'Členská sekce · nový účet',
            'title' => 'Vítej ve svém klubu.',
            'lead' => 'Tvůj účet v DaLinu je připravený. Najdeš v něm závody, přihlášky i klubové zprávy.',
            'secondary' => 'Jak začít',
            'note_label' => 'Po prvním přihlášení',
            'note' => 'Vygenerované heslo si můžeš změnit v nastavení účtu.',
            'fine' => 'S přihlášením ti pomůže správce klubu: :email. Heslo si můžeš uložit do správce hesel.',
            'fine_no_contact' => 'S přihlášením ti pomůže správce klubu. Heslo si můžeš uložit do správce hesel.',
        ],

        'reset' => [
            'eyebrow' => 'Členská sekce · reset hesla',
            'title' => 'Tvoje heslo bylo resetováno.',
            'lead' => 'Do členské sekce se nyní přihlásíš pomocí těchto údajů.',
            'secondary' => 'Nápověda k přihlášení',
            'note_label' => 'Nastavení hesla',
            'note' => 'Po přihlášení si můžeš nastavit vlastní heslo.',
            'fine' => 'Pokud potřebuješ pomoc, napiš správci klubu na :email.',
            'fine_no_contact' => 'Pokud potřebuješ pomoc, obrať se na správce klubu.',
        ],
    ],

];
