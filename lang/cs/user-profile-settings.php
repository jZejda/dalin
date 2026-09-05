<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserProfileSettings page (Other cluster)
    |--------------------------------------------------------------------------
    |
    | Stránka, kde si uživatel spravuje vlastní profil: jméno, barvu badge,
    | profilový obrázek a heslo.
    |
    */

    'navigation_label' => 'Můj profil',
    'title' => 'Můj profil',

    'form' => [
        'name' => 'Jméno a příjmení',
        'email' => 'E-mail',
        'badge_color' => 'Barva badge',
        'avatar' => 'Profilový obrázek',
        'avatar_helper' => 'Nahraj čtvercový obrázek, ořízneš si ho přímo zde. Bez obrázku se zobrazí barevný badge s iniciály.',
        'submit' => 'Uložit změny',
    ],

    'notification' => [
        'saved_title' => 'Profil uložen',
    ],

];
