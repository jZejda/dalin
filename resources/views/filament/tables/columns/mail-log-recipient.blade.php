<?php

/** @var \App\Models\MailLog $record */
$record = $getRecord();
?>

{{-- An address without a user account (deleted user, external recipient) falls back to the plain e-mail --}}
@if ($record->recipientUser)
    <x-user-identity :user="$record->recipientUser" size="sm" />
@else
    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $record->recipient }}</span>
@endif
