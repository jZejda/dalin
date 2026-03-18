<?php

declare(strict_types=1);

return [
    'enabled'        => env('DEMO_MODE', false),
    'reset_url_key'  => env('DEMO_RESET_URL_KEY', 'demo-reset-key'),
    'admin_password' => env('DEMO_ADMIN_PASSWORD', 'Demo2026!'),
];
