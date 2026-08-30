<?php

use App\Enums\AppRoles;

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
            'target_users_options' => [
                'all' => 'Všem aktivním uživatelům',
                AppRoles::Member->value => 'Členové',
                AppRoles::EventMaster->value => 'Správce závodů',
                AppRoles::Redactor->value => 'Redaktor',
                AppRoles::EventOrganizer->value => 'Organizátor závodů',
                AppRoles::BillingSpecialist->value => 'Finančník',
            ],
            'content' => 'Zpráva',
            'notification_title' => 'E-mail rozeslán',
            'notification_body' => 'Cílovým uživatelům byl odeslán e-mail.',
        ],
    ],

];
