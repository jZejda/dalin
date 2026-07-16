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

    'navigation_label' => 'Bank statement',
    'label' => 'Bank statement',
    'plural_label' => 'Bank statement',

    'id' => 'ID',
    'created_at' => 'Created',
    'user_credit_id' => 'User transaction ID',
    'user_credit_count' => 'transactions: :count',
    'variable_symbol' => 'Variable symbol',
    'note' => 'Note',
    'description' => 'Description',
    'amount' => 'Amount',

    'transaction_indicator' => [
        TransactionIndicator::Debit->value => 'expense',
        TransactionIndicator::Credit->value => 'income',
    ],

    'filters' => [
        'date_from' => 'Transaction date from',
        'date_newer' => 'Transactions newer than: :date',
        'transaction_indicator' => 'Transaction type',
    ],

    'actions' => [
        'edit_description' => [
            'label' => 'Transaction description',
            'modal_heading' => 'Edit transaction labeling',
            'modal_description' => 'For better clarity you can change the transaction\'s <strong>description</strong> and <strong>note</strong>.<br>Other transaction parameters cannot be edited. If that is really needed, contact the club account administrator.',
            'notification_title' => 'Transaction description',
            'notification_body' => 'The transaction description was successfully changed.',
        ],
        'assign_to_user' => [
            'label' => 'Assign transaction to user',
            'modal_heading' => 'Assign the transaction with VS: :vs to a specific user',
            'modal_description' => 'Incoming transactions are automatically assigned to a user <strong>if the variable symbol is stated correctly</strong>.<br>Here you can assign or unassign them manually.',
            'notification_title' => 'Transaction',
            'notification_body' => 'The incoming credit has been assigned to the user',
        ],
    ],

];
