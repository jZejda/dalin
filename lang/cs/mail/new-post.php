<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | NewPost e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a statická část těla e-mailu o nové novince v interní sekci.
    | Předmět je doplněn dynamickým titulkem novinky, tělo obsahuje dynamický
    | obsah příspěvku (nelokalizováno).
    |
    */

    'subject' => [
        'new_post' => 'Novinky',
    ],

    'body' => [
        'heading' => 'Novinka v interní sekci',
        'intro'   => 'V členské sekci byla zveřejněna novinka.',
    ],

];
