<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RichEditor custom blocks
    |--------------------------------------------------------------------------
    |
    | Překlady pro vlastní bloky RichEditoru (RichContentCustomBlocks) —
    | nadpisy, popisky formulářů a náhledové texty.
    |
    */

    'blocks' => [

        'hero' => [
            'label' => 'Hero',
            'modal_heading' => 'Konfigurace Hero bloku',
            'modal_description' => 'Nastavte parametry pro hero sekci',
            'heading' => 'Hlavní nadpis',
            'heading_placeholder' => 'Zadejte hlavní nadpis',
            'subheading' => 'Podnadpis',
            'subheading_placeholder' => 'Zadejte podnadpis (volitelné)',
            'button_label' => 'Text tlačítka',
            'button_label_placeholder' => 'Zadejte text tlačítka (volitelné)',
            'button_url' => 'URL tlačítka',
            'button_url_placeholder' => 'Zadejte URL tlačítka (volitelné)',
            'preview_untitled' => 'Nepojmenovaný hero blok',
            'preview_label' => 'Hero section: :heading',
        ],

        'alert' => [
            'label' => 'Alert',
            'modal_heading' => 'Konfigurace Alert bloku',
            'modal_description' => 'Nastavte parametry pro alert sekci',
            'type' => 'Typ alertu',
            'type_options' => [
                'default' => 'Výchozí',
                'info' => 'Informace',
                'warning' => 'Varování',
                'error' => 'Chyba',
            ],
            'heading' => 'Nadpis',
            'heading_placeholder' => 'Zadejte nadpis alertu',
            'content' => 'Obsah (Markdown)',
            'content_placeholder' => 'Zadejte obsah v Markdown formátu',
            'preview_untitled' => 'Nepojmenovaný alert',
            'preview_label' => 'Alert (:type): :heading',
        ],

        'table' => [
            'label' => 'Tabulka',
            'modal_heading' => 'Konfigurace tabulky',
            'modal_description' => 'Vlož data oddělená tabulátory (např. zkopírovaná z Excelu, ORIS nebo PDF). Sloupce lze také oddělit dvěma a více mezerami.',
            'title' => 'Nadpis (volitelný)',
            'raw' => 'Data tabulky',
            'raw_placeholder' => "kat\tdélka\tpřev.\tkontroly\nD10\t2,6\t65\t9\nD12\t2,8\t65\t10",
            'raw_helper' => 'Každý řádek = jeden řádek tabulky. Sloupce odděl tabulátorem nebo víc mezerami.',
            'has_header' => 'První řádek je hlavička',
            'striped' => 'Proužkování řádků',
            'compact' => 'Kompaktní (menší padding)',
            'preview_label_titled' => 'Tabulka: :title (:count ř.)',
            'preview_label_untitled' => 'Tabulka (:count ř.)',
        ],

        'content_divider' => [
            'label' => 'Oddělovník sekce',
            'modal_heading' => 'Konfigurace oddělovníku sekce',
            'modal_description' => 'Nastavte parametry pro oddělovník sekce',
            'separator' => 'Oddělovník',
            'separator_placeholder' => 'Např. Aktuality, O klubu, Závody...',
            'heading' => 'Hlavní nadpis',
            'heading_placeholder' => 'Zadejte hlavní nadpis sekce',
            'preview_untitled' => 'Nepojmenovaný oddělovník',
            'preview_label' => 'Oddělovník: :heading',
        ],

        'simple_divider' => [
            'label' => 'Jednoduchý oddělovník',
            'modal_heading' => 'Konfigurace jednoduchého oddělovníku',
            'modal_description' => 'Nastavte číslo lampionu a nadpis',
            'number' => 'Číslo lampionu',
            'number_placeholder' => 'Např. 31',
            'heading' => 'Nadpis',
            'heading_placeholder' => 'Zadejte nadpis',
            'preview_untitled' => 'Nepojmenovaný oddělovník',
            'preview_label' => 'Lampion #:number: :heading',
        ],

    ],

];
