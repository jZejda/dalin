<?php

declare(strict_types=1);

use App\Shared\Helpers\StartOffset;

it('computes the start minute from the event zero time', function (?string $zero, ?string $start, ?int $expected): void {
    expect(StartOffset::minutesFromZero($zero, $start))->toBe($expected);
})->with([
    'regular start' => ['10:30:00', '10:57:00', 27],
    'start at zero' => ['10:30:00', '10:30', 0],
    'start before zero' => ['10:30:00', '10:25:00', -5],
    'across an hour' => ['09:45:00', '11:02:00', 77],
    'no zero time' => [null, '10:57:00', null],
    'no start' => ['10:30:00', null, null],
    'free-text requested start' => ['10:30:00', 'co nejpozději', null],
]);
