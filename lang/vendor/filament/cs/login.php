<?php

return [

    'title' => 'Přihlášení',

    'heading' => 'Přihlášení do klientské sekce',

    'buttons' => [

        'submit' => [
            'label' => 'Přihlásit se',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'password' => [
            'label' => 'Heslo',
        ],

        'remember' => [
            'label' => 'Zapamatovat si mě',
        ],

    ],

    'messages' => [
        'failed' => 'Chybně zadané přihlašovací údaje.',
        'throttled' => 'Příliš mnoho pokusů o přihlášení. Zkuste to znovu za :seconds vteřin.',
    ],

];
