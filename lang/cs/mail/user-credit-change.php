<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserCreditChange e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu o pohybu na uživatelském kontě.
    |
    */

    'subject' => [
        'userCreditChange' => 'Pohyb na účtu uživatele',
    ],

    'club' => [
        'eyebrow' => 'Klubový účet · nový pohyb',
        'title_credit' => 'Na účtu přibylo :amount.',
        'title_debit' => 'Z účtu odešlo :amount.',
        'lead' => 'Zaznamenali jsme nový pohyb na tvém klubovém účtu.',
        'balance_label' => 'Aktuální zůstatek',
        'balance_note' => 'Stav k :date',
        'amount_label' => 'Částka pohybu',
        'date_label' => 'Datum pohybu',
        'transaction_id_label' => 'ID transakce',
        'bank_transaction_id_label' => 'ID bankovní transakce',
        'fine' => 'S dotazy k pohybu na účtu kontaktuj klub: :email.',
        'fine_no_contact' => 'S dotazy k pohybu na účtu kontaktuj klub.',
    ],

];
