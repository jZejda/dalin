<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserProfileSettings page (Other cluster)
    |--------------------------------------------------------------------------
    |
    | Page where a user manages their own profile: name, badge color,
    | profile picture and password.
    |
    */

    'navigation_label' => 'My Profile',
    'title' => 'My Profile',

    'form' => [
        'name' => 'Name',
        'email' => 'Email',
        'badge_color' => 'Badge color',
        'badge_color_helper' => 'This is the color of the round badge that marks you across the system.',
        'avatar' => 'Profile picture',
        'avatar_helper' => 'Upload a square image and crop it right here. Without a picture, a colored initials badge is shown instead.',
        'submit' => 'Save changes',
    ],

    'notification' => [
        'saved_title' => 'Profile saved',
    ],

];
