<?php
use App\Models\UserCredit;

/** @var UserCredit $userCredit  */
$userCredit = $getRecord();
?>

<div class="ml-4">
    @if(true)
        <div class="relative py-2">
            <div class="t-0 absolute left-3">
                <p class="flex h-2 w-2 items-center justify-center rounded-full bg-green-100 text-green-800 p-3 text-xs dark:bg-green-800 dark:text-green-300">10</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="file: mt-4 h-6 w-6">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
            </svg>
        </div>
    @endif
</div>
