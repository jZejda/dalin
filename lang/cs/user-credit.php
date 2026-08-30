<?php

use App\Enums\UserCreditStatus;

return [

    /*
    |--------------------------------------------------------------------------
    | UserCredit Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in UserCredit
    | resource.
    |
    */

    'navigation_label' => 'Vyúčtování akcí',
    'label' => 'Vyúčtování akcí',
    'plural_label' => 'Vyúčtování akcí',

    'id' => '#ID',
    'user' => 'Přidat uživateli',
    'user_profile' => 'Závodní profil',
    'related_user_profile' => 'Přesun ve prospěch uživatele',
    'user_source_id' => 'Založil/Změnil',
    'event_name' => 'Závod/událost',
    'status' => 'Status',
    'amount' => 'Částka',
    'currency' => 'Měna',
    'note' => 'Poznámka',

    // Credit Resource
    'form' => [
        'type_title' => 'Typ vstupu',
        'price_title' => 'Cena',
        'amount_title' => 'Částka',
        'currency_title' => 'Měna',
        'source_title' => 'Vložil/Upravil',
        'related_user_hint' => 'Vyžadováno pokud je vybrán přesun mezi uživateli.',
        'warning_tooltip' => 'Upozornění',
    ],
    'credit_type_enum' => [
        'in' => 'Vklad',
        'out' => 'Výběr',
        'donation' => 'Dar',
    ],

    'credit_status_enum' => [
        UserCreditStatus::Done->value => 'Hotovo',
        UserCreditStatus::UnAssign->value => 'Nepřiřazeno',
        UserCreditStatus::Open->value => 'Otevřeno',
    ],

    'credit_source_enum' => [
        'user' => 'Uživatel',
        'system' => 'Systém',
        'cron' => 'System',
    ],

    // Table
    'table' => [
        'created_at_title' => 'Transakce',
        'sport_event_date' => 'Datum akce',
        'sport_event_title' => 'Závod/Akce',
        'amount_title' => 'Částka',
        'source_user_title' => 'Zapsal',
        'for_user' => 'Pro uživatele:  ',
        'from_user' => 'Od uživatele:  ',
        'registration' => 'Registrace',
        'event_internal_id' => 'interní id závodu: :id',
        'comments' => 'Komentářů',
        'transaction_link' => 'Transakce: :id',
    ],

    // Filters
    'filters' => [
        'sport_event' => 'Závod',
        'unassigned_racer' => 'Není přiřazen závodník',
    ],

    // Actions
    'actions' => [
        'transport_billing' => [
            'action_group_label' => 'Cestovní rozúčtování',
            'modal_description' => 'Vyúčtování cestovních nákladů mezi členy klubu. Jedná se převod financí mezi členy klubu,
            za účelem vyrovnání cestovních nákladů. Akce vytvoří dva záznamy, jeden plusový uživateli kterému se částka připisuje.
            Druhý záznam tomu, kterému se částka strhává.',
            'modal_heading' => 'Vyúčtování cestovních nákladů.',
            'modal_submit_action_label' => 'Přidej vyúčtování',

        ],
        'transfer_between_users_billing' => [
            'action_group_label' => 'Přerozdělení mezi členy',
            'modal_description' => 'Jedná se převod financí mezi členy klubu například v případě kdy jeden člen uhradil startovné druhému členu.
            Akce vytvoří dva záznamy, jeden plusový uživateli kterému se částka připisuje.
            Druhý záznam tomu, kterému se částka strhává.',
            'modal_heading' => 'Přerozdělení financí mezi členy',
        ],
        'transfer_billing_common' => [
            'default_text' => 'Přesun financi mezi členy',
            'credit_to_user' => 'Připsat částku uživateli',
            'credit_to_user_hint' => 'Částka bude připsána na konto tohoto uživtele.',
            'credit_from_user' => 'Strhnout částku uživateli',
            'credit_from_user_hint' => 'Zvolená částka bude stržena tomuto uživateli.',
            'amount_positive_hint' => 'Je možné vložit pouze kladné hodnoty.',
            'sport_event_not_required_hint' => 'Není potřeba doplňovat.',
            'notification_title' => 'Cestovní vyúčtování',
            'notification_body' => 'Cestovný vyúčtování bylo přiřazeno uživatelům',
        ],
        'add_note' => [
            'label' => 'Poznámka',
            'modal_heading' => 'Poznámka k platbě',
            'modal_description' => 'Pokud není něco v pořádků, sem prosím napiš důvody jak to je jinak. Prosím stručně a věcně.',
            'modal_submit_action_label' => 'Uložit poznámku',
            'note_label' => 'Poznámka',
            'notification_saved_title' => 'Poznámku jsme uložili',
            'notification_saved_body' => 'Děkujeme za zaslání dotazu k vyúčtování, pokusíme se to vyřešit.',
            'notification_billing_title' => 'Poznámka k vyúčtování',
            'notification_billing_body' => 'Uživatel: :user | Vyúčtování ID: :id',
        ],
        'oris_balance' => [
            'label' => 'Načti vyúčtování z ORISu',
            'modal_heading' => 'Stáhne vyúčtování z ORIS závodu',
            'modal_description' => 'Vyber závod a načti vyúčtování. Akci můžeš provést opakovaně.',
            'modal_submit_action_label' => 'Stáhnout vyúčtování',
            'sport_event' => 'Závod/událost',
            'badge_billed' => 'Vyúčtováno',
            'badge_pending' => 'Čeká',
        ],
    ],

    'list' => [
        'navigation_label' => 'Můj kredit',
        'page_title' => 'Finance',
        'new_billing_label' => 'Nové vyúčtování',
        'new_record_label' => 'Nový záznam',
    ],

    'widgets' => [
        'header' => [
            'balance_label' => 'Aktuální stav',
            'description_positive' => 'Hurá na závody',
            'description_negative' => 'Bylo by fajn zaslat dar',
        ],
        'stats' => [
            'unassigned_amount' => 'Nepřiřazené transakce Kč',
            'unassigned_count' => 'Počet nepřiřazených',
        ],
    ],
];
