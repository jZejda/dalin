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

    'body' => [
        'heading_new_account'  => 'Password for the members section - :club.',
        'heading_reset'        => 'Password reset for the members section - :club.',
        'intro_new_account'    => 'A new account has been created for you in the :club club registration system.',
        'intro_reset'          => 'Your password has been reset in the :club club registration system.',
        'address_label'        => 'Address',
        'name_label'            => 'Name',
        'login_label'           => 'Login',
        'password_label'        => 'Password',
        'help_heading'          => 'Help / Troubleshooting',
        'help_text'             => 'For more information on how to use the application, please visit our help on [this page](:url).',
        'help_login_issues'     => 'If you have any login issues, please direct your questions to :email.',
        'help_password_change'  => 'You can change the generated password after logging into the system.',
        'tips_heading'          => 'This might come in handy',
        'tips_text'             => 'It is best to store your password in a trusted password manager such as [KeePassXC](https://keepassxc.org/), [BitWarden](https://bitwarden.com/), or similar.',
    ],

];
