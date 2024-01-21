<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel money
     |--------------------------------------------------------------------------
     */
    'locale' => config('app.locale', 'cs_CZ'),
    'defaultCurrency' => config('app.currency', 'CZK'),
    'defaultFormatter' => null,
    'defaultSerializer' => null,
    'isoCurrenciesPath' => __DIR__.'/../vendor/moneyphp/money/resources/currency.php',
    'currencies' => [
        'iso' => ['CZK', 'EUR'],
        'custom' => [
            // 'MY1' => 2,
            // 'MY2' => 3
        ],
    ],
];
