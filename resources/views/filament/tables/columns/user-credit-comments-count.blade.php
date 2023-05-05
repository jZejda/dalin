<?php
use App\Models\UserCredit;

/** @var UserCredit $userCredit  */
$userCredit = $getRecord();
$count = $userCredit->userCreditNoteCount();
?>

<div class="ml-4">
    @if($count > 0)
        <div class="relative py-2">
            <div class="t-0 absolute left-3">
                <p class="flex h-2 w-2 items-center justify-center rounded-full bg-green-100 text-green-800 p-3 text-xs dark:bg-green-800 dark:text-green-300">{{ $count }}</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="file: mt-4 h-6 w-6">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M8 9h8"></path>
                <path d="M8 13h6"></path>
                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path>
            </svg>
        </div>
    @endif
</div>
