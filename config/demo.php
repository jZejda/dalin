<?php

declare(strict_types=1);

return [
    'enabled'             => env('DEMO_MODE', false),
    'reset_url_key'       => env('DEMO_RESET_URL_KEY', 'demo-reset-key'),
    'admin_password'      => env('DEMO_ADMIN_PASSWORD', 'Demo2026!'),

    // Přepínač pro automatické snímkování aplikace (Playwright) do dokumentace —
    // po navštívení /demo-screenshot-mode/{screenshot_mode_key} zmizí horní DEMO
    // lišta pro daný prohlížeč (cookie), aniž by se muselo vypínat DEMO_MODE.
    'screenshot_mode_key' => env('DEMO_SCREENSHOT_MODE_KEY', 'demo-screenshot-key'),
];
