<?php

declare(strict_types=1);

use App\Services\Mail\MailBranding;

it('picks dark ink on light accents and white on dark ones', function (string $accent, string $expected): void {
    expect(MailBranding::contrastText($accent))->toBe($expected);
})->with([
    'yellow' => ['#ffd329', MailBranding::INK],
    'white' => ['#ffffff', MailBranding::INK],
    'forest green' => ['#1f7a5c', '#ffffff'],
    'navy' => ['#1e3a8a', '#ffffff'],
]);

it('mixes the accent into white for soft highlights', function (): void {
    expect(MailBranding::mixWithWhite('#000000', 0.5))->toBe('#808080')
        ->and(MailBranding::mixWithWhite('#ffd329', 0.0))->toBe('#ffffff')
        ->and(MailBranding::mixWithWhite('#1f7a5c', 1.0))->toBe('#1f7a5c');
});

it('labels social profiles by their network', function (string $url, string $label): void {
    expect(MailBranding::socialLabel($url))->toBe($label);
})->with([
    ['https://www.facebook.com/skdemo', 'Facebook'],
    ['https://instagram.com/skdemo', 'Instagram'],
    ['https://m.facebook.com/skdemo', 'Facebook'],
    ['https://www.strava.com/clubs/123', 'Strava'],
    ['https://twitter.com/skdemo', 'X'],
    ['https://www.mujklub.cz/', 'mujklub.cz'],
]);
