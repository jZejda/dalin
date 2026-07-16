<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content module (Pages, Posts, ContentCategories)
    |--------------------------------------------------------------------------
    |
    | Překlady pro správu obsahu - stránky (PageResource), novinky
    | (PostResource) a kategorie obsahu (ContentCategoryResource).
    |
    */

    'page' => [
        'navigation_label' => 'Stránky',
        'label'            => 'Stránka',
        'plural_label'     => 'Stránky',

        'form' => [
            'content'          => 'Obsah',
            'author'           => 'Author',
            'format'           => 'Formát',
            'category'         => 'Kategorie',
            'show_category_menu' => 'Zobrazit menu kategorie?',
            'weight'           => 'Váha',
            'meta'             => 'Meta',
            'meta_key'         => 'Klíč',
            'meta_value'       => 'Hodnota',
        ],

        'table' => [
            'title'  => 'Název',
            'author' => 'Autor',
            'format' => 'Formát obsahu',
        ],

        'search' => [
            'author'   => 'Autor',
            'category' => 'Zařazeno',
        ],

        'relation' => [
            'title' => 'Stránka(y)',
        ],
    ],

    'post' => [
        'navigation_label' => 'Novinky',
        'label'            => 'Novinka',
        'plural_label'     => 'Novinky',

        'form' => [
            'title'                    => 'Nadpis',
            'content'                  => 'Obsah novinky',
            'section_additional'       => 'Dodatečné informace',
            'section_additional_description' => 'Editorial pro souhrn novinky - nepovinné - dostupné po rozkliknutí',
            'private'                  => 'Interní novinka',
            'author'                   => 'Autor',
            'format'                   => 'Formát',
        ],

        'table' => [
            'author' => 'Autor',
            'format' => 'Formát obsahu',
        ],

        'search' => [
            'author'   => 'Autor',
            'status'   => 'Stav',
            'private'  => 'Neveřejná',
            'public'   => 'Veřejná',
            'not_available' => 'N/A',
        ],

        'actions' => [
            'send_news_email' => [
                'label'                     => 'Pošli e-mail',
                'modal_heading'             => 'Pošle e-mail k novince',
                'modal_description'        => 'E-mail je odeslán sepárátně každému uživateli zvlášť. Pokud zvolíte zaslat zprávu všem, bude tato odeslána bez ohledu na uživatelské preferenci.',
                'modal_submit_action_label' => 'Odeslat',
                'notification_title'       => 'E-mail novinky rozeslán',
                'notification_body'        => 'Zvoleným uživatelům byl odeslán e-mail.',
                'subject'                   => 'Předmět zprávy',
                'selection'                 => 'Zvolte možnost',
                'selection_interested'      => 'Uživatelé kteří mají zájem o novinky',
                'selection_all'             => 'Všem aktivním uživatelům systému',
            ],
        ],
    ],

    'category' => [
        'search' => [
            'slug'        => 'Slug',
            'pages_count' => 'Počet příspěvků',
        ],
    ],

];
