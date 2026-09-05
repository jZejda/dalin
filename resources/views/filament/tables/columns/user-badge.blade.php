<?php

use Illuminate\Support\Str;

// The column name is the dotted relation path used for sorting/searching (e.g. "sourceUser.name");
// the relation is everything before the last segment.
$relation = Str::beforeLast($column->getName(), '.');

/** @var \App\Models\User|null $user */
$user = data_get($getRecord(), $relation);
?>

@if ($user)
    <x-user-badge
        :initials="$user->initials"
        :color="$user->badge_color"
        :avatar-url="$user->avatar_url"
        size="sm"
    />
@else
    <x-user-badge initials="N/A" size="sm" />
@endif
