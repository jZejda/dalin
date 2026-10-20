<?php

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Enums\PostStatus;

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

    'enums' => [
        'content_format' => [
            ContentFormat::Html->value => 'HTML',
            ContentFormat::Markdown->value => 'Markdown',
            ContentFormat::TipTapJson->value => 'TipTap JSON',
        ],

        'page_status' => [
            PageStatus::Open->value => 'Zveřejněno',
            PageStatus::Closed->value => 'Uzavřeno',
            PageStatus::Draft->value => 'Koncept',
            PageStatus::Archive->value => 'Archivováno',
        ],

        'post_status' => [
            PostStatus::Public->value => 'Veřejná',
            PostStatus::Private->value => 'Interní',
        ],
    ],

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
            'section_seo'      => 'Vyhledávače a sociální sítě',
            'section_seo_description' => 'Nepovinné. Prázdný titulek převezme název stránky, prázdný popis začátek jejího textu.',
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
            'section_seo'              => 'Vyhledávače a sociální sítě',
            'section_seo_description'  => 'Nepovinné. Prázdný titulek převezme název novinky, prázdný popis editorial nebo začátek textu.',
            'private'                  => 'Interní novinka',
            'author'                   => 'Autor',
            'format'                   => 'Formát',
            'format_helper_edit'       => 'Formát hotové novinky nejde přepnout. Markdown novinku převedete akcí „Převést na TipTap“ v záhlaví.',
            'cover'                    => 'Titulní obrázek',
            'cover_helper'             => 'Na titulní stránce se zobrazí na kartě novinky ve formátu 4 : 3, v záhlaví detailu jako široký pruh 7 : 2. Oba výřezy se berou ze středu obrázku – nejlépe nahrajte neořezanou fotku s hlavním motivem uprostřed. JPG, PNG nebo WebP, max. 8 MB.',
            'section_gallery'          => 'Fotogalerie',
            'section_gallery_description' => 'Fotky ze závodu nebo tréninku se zobrazí pod článkem jako mřížka náhledů. Pořadí změníte přetažením. JPG, PNG nebo WebP, max. 8 MB na fotku, nejvýše :max fotek. Celá alba ze závodů patří na Rajče – stačí přidat odkaz.',
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
            'convert_to_tiptap' => [
                'label'              => 'Převést na TipTap',
                'modal_heading'      => 'Převést novinku na TipTap?',
                'modal_description'  => 'Text se převede z Markdownu do vizuálního editoru. Převod nejde vrátit zpět. Obrázky a odkazy zůstanou, zkontrolujte ale formátování.',
                'modal_submit'       => 'Převést',
                'notification_title' => 'Novinka převedena na TipTap',
            ],
        ],

        'public' => [
            'breadcrumb_label' => 'Drobečková navigace',
            'breadcrumb_home'  => 'Úvod',
            'breadcrumb_news'  => 'Novinky',
            'author'           => 'Autor',
            'published'        => 'Publikováno',
            'more_link'        => 'Ostatní novinky',
            'index_lead'       => 'Zprávy z oddílu, pozvánky na akce a výsledky – od nejnovějších.',
            'index_empty'      => 'Zatím tu nejsou žádné novinky. Brzy se dozvíte, co se v klubu děje.',
            'index_page_title' => 'Novinky – strana :page',
            'gallery_heading'  => 'Fotogalerie',
            'gallery_photo_alt' => 'Fotografie :number – :title',
            'gallery_close'    => 'Zavřít',
            'gallery_previous' => 'Předchozí fotka',
            'gallery_next'     => 'Další fotka',
            'gallery_counter'  => 'Fotka :current z :total',
        ],
    ],

    'category' => [
        'search' => [
            'slug'        => 'Slug',
            'pages_count' => 'Počet příspěvků',
        ],
    ],

];
