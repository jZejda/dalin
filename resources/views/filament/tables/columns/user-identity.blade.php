<?php

use App\Models\User;

/** @var User|null $user */
$user = $getRecord()->user;
?>

@if ($user)
    <x-user-identity :user="$user" size="sm" />
@endif
