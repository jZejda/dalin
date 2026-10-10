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

    'club' => [
        'eyebrow' => 'Klubové novinky · nový článek',
        'title' => 'Co je nového v klubu.',
        'lead' => 'V členské sekci přibyla nová zpráva.',
    ],

];
