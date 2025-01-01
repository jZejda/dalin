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

    'id' => 'ID',
    'created_at' => 'Vytvořeno',
    'user_credit_id' => 'ID transakce uživatele',
    'variable_symbol' => 'Variabilní symbol',
    'note' => 'Poznámka',
    'description' => 'Popis',
    'amount' => 'Částka',

    'transaction_indicator' => [
        TransactionIndicator::Debit->value => 'výdaj',
        TransactionIndicator::Credit->value => 'příjem',
    ],

];
