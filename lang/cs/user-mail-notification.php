<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Mail Notification Page
    |--------------------------------------------------------------------------
    |
    | Překlady pro stránku uživatelských nastavení (UserMailNotification).
    |
    */

    'navigation_label' => 'Uživatelská nastavení',
    'title'            => 'Uživatelská nastavení',

    'breadcrumbs' => [
        'users'    => 'Uživatel',
        'settings' => 'Nastavení',
    ],

    'tabs' => [
        'group_label'      => 'Tabs',
        'mail_settings'    => 'E-mailové nastavení',
        'display_filters'  => 'Filtry zobrazení',
        'other'            => 'Ostatní',
        'api_key'          => 'API Klíč',
        'calendar'         => 'Kalendář',
    ],

    'sections' => [
        'news' => [
            'heading'     => 'Upozornění na Novinky',
            'description' => 'Zde si můžete nastavit upozornění na novinky veřejné a novinky z členské sekce.',
        ],
        'entry_deadline' => [
            'heading'     => 'Upozornění na blížící se konec přihlášek k závodům',
            'description' => 'Pokud se bude blížit konec přihlášek k závodům, budete na toto upozorněni v e-mailu v uvedený čas s předstihem nastaveným ve volbě počtu dnů před koncem přihlášek.',
        ],
        'weekly_summary' => [
            'heading'     => 'Souhrn závodů u kterých končí termín přihlášek následující týden',
            'description' => 'V nastaveni definujete, které sporty budou v e-mailu souhrnně uvedeny. Souhrn obsahuje závody u kterých končí termín přihlášek následující týden.',
        ],
        'other_mails' => [
            'heading'     => 'Ostatní e-maily',
            'description' => 'Souhrný e-mail před závodem obsahuje informace o akci, startovní časy přihlášených závodníků a parametry jejich kategorií.',
        ],
        'sign_up_permissions' => [
            'heading'     => 'Oprávnění k přihlašování',
            'description' => 'V nastavení můžete udělit právo přihlašovat všechny vámi spravované registrace vybraným uživatelům. Vhodné například pro rodinné příslušníky, kamarády. Právo můžete kdykoliv odvolat.',
        ],
        'api_key' => [
            'heading'     => 'Správa API klíče',
            'description' => 'API klíč slouží pro autentizaci při používání API. Uchovávejte jej v tajnosti.',
        ],
        'calendar' => [
            'heading'     => 'Kalendářové feedy',
            'description' => 'Veřejné feedy jsou dostupné bez přihlášení. Osobní feedy vyžadují vygenerování tokenu a zobrazují pouze vaše závody a tréninky. Feedy lze přidat do Google Calendar, Apple Calendar a dalších aplikací podporujících iCal.',
        ],
    ],

    'form' => [
        'news'                         => 'Novinky',
        'news_option_public'           => 'Novinky veřejné',
        'news_option_members'          => 'Novinky členské sekce',
        'sport'                        => 'Sport',
        'sport_time_trigger'           => 'Přibližná hodina upozornění',
        'days_before_event_entry_ends' => 'Dnů před ukončením přihlášek',
        'week_report_by_sport'         => 'Sport',
        'pre_race_summary_enabled'     => 'Souhrn před závodem',
        'pre_race_summary_days_before' => 'Dnů před závodem',
        'pre_race_summary_time_trigger' => 'Přibližná hodina odeslání',
        'event_filters'                => 'Uživatelské filtry listu závodů a událostí.',
        'event_filters_add_action'     => 'Přidej nový filtr',
        'filter_name'                  => 'Název',
        'filter_name_hint'             => 'Bude zobrazen jako titulek filtru.',
        'filter_sport_list'            => 'Sport',
        'filter_sport_event_type'      => 'Typ akce',
        'filter_icon'                  => 'Ikona',
        'users_allow_sign_up_for_race' => 'Uživatelé kteří mě mohou přihlašovat a odhlašovat ze závodů',
    ],

    'common' => [
        'copied_title' => 'Zkopírováno',
        'error_title'  => 'Chyba',
    ],

    'actions' => [
        'generate_api_key' => [
            'notification_title' => 'API klíč vygenerován',
            'notification_body'  => 'Nový API klíč byl úspěšně vygenerován. Hash klíče je zobrazen níže a zůstane viditelný i po obnovení stránky.',
        ],
        'regenerate_api_key' => [
            'notification_title' => 'API klíč přegenerován',
            'notification_body'  => 'API klíč byl úspěšně přegenerován. Starý klíč již není platný. Nový hash klíče je zobrazen níže.',
        ],
        'delete_api_key' => [
            'notification_title' => 'API klíč smazán',
            'notification_body'  => 'API klíč byl úspěšně smazán.',
        ],
        'copy_api_key' => [
            'notification_body' => 'API klíč byl zkopírován do schránky.',
        ],
        'generate_calendar_token' => [
            'notification_title' => 'Kalendářový token vygenerován',
            'notification_body'  => 'Nový token byl úspěšně vygenerován.',
        ],
        'regenerate_calendar_token' => [
            'notification_title' => 'Kalendářový token přegenerován',
            'notification_body'  => 'Starý token byl zneplatněn. Nový token byl úspěšně vygenerován.',
        ],
        'revoke_calendar_token' => [
            'notification_title' => 'Kalendářový token zrušen',
            'notification_body'  => 'Token byl úspěšně zrušen.',
        ],
        'copy_calendar_token' => [
            'notification_body' => 'Kalendářový token byl zkopírován do schránky.',
        ],
        'copy_calendar_url' => [
            'notification_body' => 'Adresa kalendářového kanálu byla zkopírována do schránky.',
        ],
        'copy_calendar_url_failed' => [
            'notification_body' => 'Adresu se nepodařilo zkopírovat do schránky.',
        ],
        'submit' => [
            'notification_title' => 'Nastavení uloženo',
            'notification_body'  => 'Změny v nastavení byly uloženy.',
        ],
    ],

];
