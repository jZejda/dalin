<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EntryEndsToPay e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the entry deadline reminder e-mail.
    |
    */

    'subject' => [
        'entry_ends_to_pay' => 'Entry fees due – :term entry deadline',
    ],

    'club' => [
        'eyebrow' => 'For the billing team · entry fees',
        'title' => 'Time to pay the entry fees.',
        'lead' => 'The :term entry deadline is ending. Please pay the entry fees for the entered members at these races.',
        'fine' => 'Check the payment details and the exact amount in the organiser\'s documents. This overview is a deadline reminder.',
        'fine_with_payments' => 'Amounts follow the club\'s ORIS settlement at the time shown – entries may still change until the deadline. For races without payment details, check the organiser\'s documents.',
    ],

    'payment' => [
        'to_pay' => 'To pay',
        'paid' => 'Paid according to ORIS (:amount)',
        'breakdown' => 'entry fees :entry · SI rental :si · services :service · already paid :paid',
        'bank_account' => 'Account',
        'vs' => 'Var. symbol',
        'fetched_at' => 'ORIS status at :time',
        'qr_alt' => 'QR payment',
        'qr_caption' => 'QR payment',
    ],

];
