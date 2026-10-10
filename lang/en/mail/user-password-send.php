<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserPasswordSend e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the portal password send/reset e-mail.
    |
    */

    'subject' => [
        'send_password'  => 'Sending portal password',
        'reset_password' => 'Portal password reset',
    ],

    'club' => [
        'name_label' => 'Name',
        'email_label' => 'Login e-mail',
        'password_label' => 'Generated password',
        'url_label' => 'Members\' section',
        'action' => 'Log in to DaLin',

        'new_account' => [
            'eyebrow' => 'Members\' section · new account',
            'title' => 'Welcome to your club.',
            'lead' => 'Your DaLin account is ready. You will find races, entries and club news in it.',
            'secondary' => 'Getting started',
            'note_label' => 'After your first login',
            'note' => 'You can change the generated password in your account settings.',
            'fine' => 'The club administrator will help you with logging in: :email. You can store the password in a password manager.',
            'fine_no_contact' => 'The club administrator will help you with logging in. You can store the password in a password manager.',
        ],

        'reset' => [
            'eyebrow' => 'Members\' section · password reset',
            'title' => 'Your password has been reset.',
            'lead' => 'You can now log in to the members\' section with these details.',
            'secondary' => 'Help with logging in',
            'note_label' => 'Setting a password',
            'note' => 'After logging in you can set your own password.',
            'fine' => 'If you need help, write to the club administrator at :email.',
            'fine_no_contact' => 'If you need help, contact the club administrator.',
        ],
    ],

];
