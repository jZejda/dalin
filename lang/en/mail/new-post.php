<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | NewPost e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and static body part of the new post in the members section
    | e-mail. The subject is extended with a dynamic post title, the body
    | contains dynamic post content (not localized).
    |
    */

    'subject' => [
        'new_post' => 'News',
    ],

    'body' => [
        'heading' => 'New post in the members section',
        'intro'   => 'A new post has been published in the members section.',
    ],

];
