<?php

use App\Enums\MarketOfferStatus;
use App\Enums\MarketOrderStatus;
use App\Enums\MarketPaymentMethod;

return [

    /*
    |--------------------------------------------------------------------------
    | Marketplace Module
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings for the
    | marketplace module (tržiště) — offers, products and orders.
    |
    */

    'offer_status_enum' => [
        MarketOfferStatus::Active->value => 'Aktivní',
        MarketOfferStatus::Closed->value => 'Uzavřená',
        MarketOfferStatus::Billed->value => 'Rozúčtovaná',
    ],

    'order_status_enum' => [
        MarketOrderStatus::Ordered->value => 'Objednáno',
        MarketOrderStatus::Cancelled->value => 'Zrušeno',
        MarketOrderStatus::Billed->value => 'Rozúčtováno',
    ],

    'payment_method_enum' => [
        MarketPaymentMethod::DirectPayment->value => 'Přímá platba',
        MarketPaymentMethod::CreditCharge->value => 'Stržení z konta',
    ],

    'navigation_group' => 'Tržiště',
    'marketplace_title' => 'Tržiště',
    'my_offers_title' => 'Moje nabídky',
    'my_orders_title' => 'Moje objednávky',
    'created_at' => 'Vytvořeno',
    'close_action' => 'Zavřít',

    'tab_active' => 'Aktivní nabídky',
    'tab_past' => 'Proběhlé nabídky',

    'product' => 'Produkt',
    'product_name' => 'Název',
    'product_description' => 'Popis',
    'product_url' => 'Odkaz na produkt',
    'product_image' => 'Obrázek',
    'unit_price' => 'Cena za kus',
    'unit_price_helper' => '0 = zdarma / výměna',
    'free_badge' => 'Zdarma / výměna',
    'price_suffix' => 'Kč',
    'payment_method' => 'Způsob úhrady',
    'qty_available' => 'Počet kusů',
    'qty_available_helper' => 'Nech prázdné pro neomezené množství.',
    'qty_remaining' => 'Zbývá',
    'qty_unlimited' => 'Neomezeno',
    'sold_out' => 'Vyprodáno',

    'offer' => 'Nabídka',
    'offer_title' => 'Název nabídky',
    'offer_description' => 'Popis nabídky',
    'offer_author' => 'Nabízí',
    'offer_status' => 'Stav',
    'offer_closes_at' => 'Objednávky do',
    'offer_closed_at' => 'Uzavřeno',
    'club_offer' => 'Oddílová nabídka',
    'club_offer_helper' => 'Nabídka za oddíl — rozúčtování provede pokladník.',
    'club_offer_badge' => 'Oddílová',
    'products' => 'Produkty',
    'products_count' => 'Produktů',
    'orders_count' => 'Objednávek',
    'products_locked_helper' => 'Po první objednávce je cena a způsob úhrady uzamčen a produkty nelze mazat.',

    'offer_author_column' => 'Nabízí',

    'create_offer' => 'Vystavit nabídku',
    'edit_offer' => 'Upravit nabídku',
    'close_offer' => 'Uzavřít nabídku',
    'close_offer_confirmation' => 'Členové už nebudou moci objednávat ani rušit objednávky. Všem zúčastněným se rozešle e-mail. Tuto akci nelze vrátit.',
    'offer_closed_notification' => 'Nabídka byla uzavřena',
    'show_orders' => 'Objednávky',

    'bill_offer' => 'Rozúčtovat',
    'bill_offer_confirmation' => 'Objednávky se stržením z konta se naúčtují objednatelům z klientského konta, ostatní se označí jako vyřízené. Tuto akci nelze vrátit.',
    'offer_billed_notification' => 'Nabídka rozúčtována',
    'offer_billed_notification_body' => 'Rozúčtováno objednávek: :count',
    'bill_error_status' => 'Rozúčtovat lze jen uzavřenou nabídku.',

    'send_announcement' => 'Poslat e-mail členům',
    'send_announcement_confirmation' => 'Odešle upozornění na tuto nabídku všem aktivním členům klubu.',
    'announcement_sent_notification' => 'E-mail členům odeslán',
    'announcement_sent_notification_body' => 'Počet adresátů: :count',

    'mail' => [
        'offer_closed_subject' => 'Nabídka na tržišti byla ukončena',
        'announcement_subject' => 'Nová nabídka na tržišti',
    ],

    'order_action' => 'Objednat',
    'order_qty' => 'Počet kusů',
    'order_note' => 'Poznámka',
    'order_total' => 'Celkem',
    'order_created_notification' => 'Objednávka odeslána',
    'order_error_qty' => 'Počet kusů musí být alespoň 1.',
    'order_error_closed' => 'Nabídka už není otevřená pro objednávky.',
    'order_error_sold_out' => 'Tolik kusů už není k dispozici, zbývá :remaining.',

    'cancel_order' => 'Zrušit objednávku',
    'cancel_order_confirmation' => 'Objednané kusy se uvolní pro ostatní. Objednávku pak můžeš vytvořit znovu, dokud je nabídka aktivní.',
    'order_cancelled_notification' => 'Objednávka zrušena',
    'cancel_error_status' => 'Tuto objednávku už nelze zrušit.',
    'cancel_error_closed' => 'Nabídka už je uzavřená, objednávku nelze zrušit.',
    'close_error_status' => 'Nabídka už není aktivní.',

    'ordered_at' => 'Objednáno',
    'ordered_by' => 'Objednal',

    'empty_marketplace' => 'Žádné aktivní nabídky',
    'empty_marketplace_description' => 'Jakmile někdo vystaví nabídku, uvidíš ji tady.',
    'empty_marketplace_past' => 'Zatím žádné proběhlé nabídky',
    'empty_my_offers' => 'Zatím nemáš žádné nabídky',
    'empty_my_offers_description' => 'Vystav věci, které nabízíš ostatním členům.',
    'empty_my_orders' => 'Zatím nemáš žádné objednávky',
    'empty_my_orders_description' => 'Prohlédni si tržiště a objednej si něco.',
    'browse_marketplace' => 'Prohlédnout tržiště',
    'no_orders_yet' => 'Zatím žádné objednávky.',

];
