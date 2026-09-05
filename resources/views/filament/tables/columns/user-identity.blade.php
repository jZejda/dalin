<?php

use Illuminate\Support\Str;

// The column name is the dotted relation path used for sorting/searching (e.g. "user.name",
// "user.userIdentification"); the relation is everything before the last segment.
$relation = Str::beforeLast($column->getName(), '.');

/** @var \App\Models\User|null $user */
$user = data_get($getRecord(), $relation);
?>

@if ($user)
    <x-user-identity :user="$user" size="sm" />
@endif
