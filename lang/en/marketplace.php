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
        MarketOfferStatus::Active->value => 'Active',
        MarketOfferStatus::Closed->value => 'Closed',
        MarketOfferStatus::Billed->value => 'Billed',
    ],

    'order_status_enum' => [
        MarketOrderStatus::Ordered->value => 'Ordered',
        MarketOrderStatus::Cancelled->value => 'Cancelled',
        MarketOrderStatus::Billed->value => 'Billed',
    ],

    'payment_method_enum' => [
        MarketPaymentMethod::DirectPayment->value => 'Direct payment',
        MarketPaymentMethod::CreditCharge->value => 'Account deduction',
    ],

    'navigation_group' => 'Marketplace',
    'marketplace_title' => 'Marketplace',
    'my_offers_title' => 'My offers',
    'my_orders_title' => 'My orders',
    'created_at' => 'Created',
    'close_action' => 'Close',

    'tab_active' => 'Active offers',
    'tab_past' => 'Past offers',

    'product' => 'Product',
    'product_name' => 'Name',
    'product_description' => 'Description',
    'product_url' => 'Product link',
    'product_image' => 'Image',
    'unit_price' => 'Unit price',
    'unit_price_helper' => '0 = free / exchange',
    'free_badge' => 'Free / exchange',
    'price_suffix' => 'CZK',
    'payment_method' => 'Payment method',
    'qty_available' => 'Quantity available',
    'qty_available_helper' => 'Leave empty for unlimited quantity.',
    'qty_remaining' => 'Remaining',
    'qty_unlimited' => 'Unlimited',
    'sold_out' => 'Sold out',

    'offer' => 'Offer',
    'offer_title' => 'Offer title',
    'offer_description' => 'Offer description',
    'offer_author' => 'Offered by',
    'offer_status' => 'Status',
    'offer_closes_at' => 'Orders until',
    'offer_closed_at' => 'Closed',
    'club_offer' => 'Club offer',
    'club_offer_helper' => 'Offer on behalf of the club — billing is handled by the billing specialist.',
    'club_offer_badge' => 'Club',
    'products' => 'Products',
    'products_count' => 'Products',
    'orders_count' => 'Orders',
    'products_locked_helper' => 'After the first order, the price and payment method are locked and products cannot be deleted.',

    'offer_author_column' => 'Offered by',

    'create_offer' => 'Create offer',
    'edit_offer' => 'Edit offer',
    'close_offer' => 'Close offer',
    'close_offer_confirmation' => 'Members will no longer be able to order or cancel orders. All participants will be sent an email. This action cannot be undone.',
    'offer_closed_notification' => 'The offer has been closed',
    'show_orders' => 'Orders',

    'bill_offer' => 'Bill',
    'bill_offer_confirmation' => 'Orders with account deduction will be charged to the ordering users\' accounts, others will be marked as settled. This action cannot be undone.',
    'offer_billed_notification' => 'Offer billed',
    'offer_billed_notification_body' => 'Orders billed: :count',
    'bill_error_status' => 'Only a closed offer can be billed.',

    'send_announcement' => 'Send email to members',
    'send_announcement_confirmation' => 'Sends a notification about this offer to all active club members.',
    'announcement_sent_notification' => 'Email sent to members',
    'announcement_sent_notification_body' => 'Number of recipients: :count',

    'mail' => [
        'offer_closed_subject' => 'The marketplace offer has ended',
        'announcement_subject' => 'New marketplace offer',

        'announcement_heading' => 'New marketplace offer',
        'announcement_intro' => '**:user** created an offer **:title**.',
        'announcement_intro_club' => '**:user** created an offer on behalf of the club **:title**.',
        'announcement_orders_until' => 'Orders until **:date**.',
        'announcement_products_heading' => 'Offered products:',
        'announcement_price_per_unit' => ':price CZK/pc',
        'qty_suffix' => ':qty pcs',
        'free_product_label' => 'free / exchange',

        'offer_closed_heading' => 'The marketplace offer has ended',
        'offer_closed_intro' => 'The offer **:title** by **:author** has ended :date.',
        'offer_closed_author_note' => 'You can find members\' orders in the app on the My offers page. Once the purchase is completed, you can bill the offer according to the ordered pieces.',
        'offer_closed_orders_heading' => 'Your orders in this offer:',
        'offer_closed_unit_price' => '× :price CZK',
        'offer_closed_total_price' => '**:total CZK**',
        'offer_closed_footer_credit' => 'Items with account deduction will be charged to you when the offer is billed.',
        'offer_closed_footer_direct' => 'Direct payments will happen after arrangement with the offer author.',
    ],

    'order_action' => 'Order',
    'order_qty' => 'Quantity',
    'order_note' => 'Note',
    'order_total' => 'Total',
    'order_created_notification' => 'Order sent',
    'order_error_qty' => 'The quantity must be at least 1.',
    'order_error_closed' => 'The offer is no longer open for orders.',
    'order_error_sold_out' => 'That many pieces are no longer available, :remaining remaining.',

    'cancel_order' => 'Cancel order',
    'cancel_order_confirmation' => 'The ordered pieces will be released for others. You can create the order again as long as the offer is active.',
    'order_cancelled_notification' => 'Order cancelled',
    'cancel_error_status' => 'This order can no longer be cancelled.',
    'cancel_error_closed' => 'The offer is already closed, the order cannot be cancelled.',
    'close_error_status' => 'The offer is no longer active.',

    'ordered_at' => 'Ordered',
    'ordered_by' => 'Ordered by',

    'empty_marketplace' => 'No active offers',
    'empty_marketplace_description' => 'Once someone creates an offer, you\'ll see it here.',
    'empty_marketplace_past' => 'No past offers yet',
    'empty_my_offers' => 'You don\'t have any offers yet',
    'empty_my_offers_description' => 'Offer items to other members.',
    'empty_my_orders' => 'You don\'t have any orders yet',
    'empty_my_orders_description' => 'Browse the marketplace and order something.',
    'browse_marketplace' => 'Browse marketplace',
    'no_orders_yet' => 'No orders yet.',

];
