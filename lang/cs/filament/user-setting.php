<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserSettings page
    |--------------------------------------------------------------------------
    |
    | Překlady pro stránku Můj přehled (UserSettings) a související akce
    | (odeslání e-mailu, změna hesla).
    |
    */

    'heading' => 'Můj přehled',
    'changelog_note' => 'Přehled změn aplikace dalin najdeš v dokumentaci.',

    'actions' => [
        'send_mail' => [
            'label' => 'Pošli e-mail',
            'modal_heading' => 'Pošle e-mail vybraným skupinám uživatelů systému.',
            'modal_description' => 'E-mail je odesílán z fronty každý 5 minut.',
            'modal_submit' => 'Odeslat',
            'subject' => 'Předmět zprávy',
            'reply_to' => 'Adresa pro odpovědi',
            'target_users' => 'Cílová skupina uživatelů podle role',
            'target_users_helper' => 'E-mail dostanou jen aktivní uživatelé. Kdo spadá do více vybraných skupin, dostane ho jen jednou.',
            'target_users_all' => 'Všem aktivním uživatelům',
            'content' => 'Zpráva',
            'notification_title' => 'E-mail rozeslán',
            'notification_body' => '{1} E-mail byl zařazen k odeslání :count uživateli.|[2,*] E-mail byl zařazen k odeslání :count uživatelům.',
            'notification_empty_title' => 'E-mail nebyl odeslán',
            'notification_empty_body' => 'Zvoleným skupinám neodpovídá žádný aktivní uživatel. Zkontrolujte výběr cílové skupiny.',
        ],
    ],

];
