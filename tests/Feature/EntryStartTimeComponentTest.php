<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;

it('shows the start minute from zero above the real start time', function (): void {
    $html = (string) view('components.entry-start-time', [
        'start' => Carbon::parse('2026-10-17 10:57:00'),
        'zeroTime' => '10:30:00',
    ]);

    expect($html)->toContain('>27</span>')
        ->toContain('>10:57</span>')
        ->toContain('font-mono')
        ->not->toContain('+27');
});

it('falls back to the plain start time when the event has no zero time', function (): void {
    $html = (string) view('components.entry-start-time', [
        'start' => Carbon::parse('2026-10-17 10:57:00'),
        'zeroTime' => null,
    ]);

    expect($html)->toContain('10:57')
        ->not->toContain('font-mono');
});
