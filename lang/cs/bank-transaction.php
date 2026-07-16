<?php

use App\Services\Bank\Enums\TransactionIndicator;

return [

    /*
    |--------------------------------------------------------------------------
    | BankTransaction Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings for BankTransaction
    | resource.
    |
    */

    // Indicator

    'navigation_label' => 'Bankovní výpis',
    'label' => 'Bankovní výpis',
    'plural_label' => 'Bankovní výpis',

    'id' => 'ID',
    'created_at' => 'Vytvořeno',
    'user_credit_id' => 'ID transakce uživatele',
    'user_credit_count' => 'transakcí: :count',
    'variable_symbol' => 'Variabilní symbol',
    'note' => 'Poznámka',
    'description' => 'Popis',
    'amount' => 'Částka',

    'transaction_indicator' => [
        TransactionIndicator::Debit->value => 'výdaj',
        TransactionIndicator::Credit->value => 'příjem',
    ],

    'filters' => [
        'date_from' => 'Datum transakce od',
        'date_newer' => 'Transakce novější: :date',
        'transaction_indicator' => 'Typ transakce',
    ],

    'actions' => [
        'edit_description' => [
            'label' => 'Popis transakce',
            'modal_heading' => 'Upravit označení transakce',
            'modal_description' => 'Pro lepší přehlednost můžeš u transakce změnit <strong>popis</strong> a <strong>poznámku</strong>.<br>Ostatní parametry transakce není možné upravovat. V případě že by to opravdu bylo potřeba, kontaktuj správce účtu klubu.',
            'notification_title' => 'Popis transakce',
            'notification_body' => 'Úspěšně jsme změnili popis transakce.',
        ],
        'assign_to_user' => [
            'label' => 'Přiradit transakci uživateli',
            'modal_heading' => 'Přidání transakce konkrétnímu uživateli s VS: :vs',
            'modal_description' => 'Příchozí transakce jsou uživateli <strong>pokud je správně uveden variabilní symbol</strong> automaticky přiřazeny.<br>Zde je můžeš přiřadit nebo zrušit ručně.',
            'notification_title' => 'Transakce',
            'notification_body' => 'Příchozí kredit byl přiřazen uživateli',
        ],
    ],

];
