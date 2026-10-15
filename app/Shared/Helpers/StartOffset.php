<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

/**
 * Start minute relative to the event's zero time ("00" = sport_events.start_time).
 */
final class StartOffset
{
    /**
     * @param string|null $zeroTime event zero time, e.g. "10:30:00"
     * @param string|null $start    racer's start time, e.g. "10:57:00" (any "HH:MM…" string)
     */
    public static function minutesFromZero(?string $zeroTime, ?string $start): ?int
    {
        $zeroMinutes = self::toMinutes($zeroTime);
        $startMinutes = self::toMinutes($start);

        return $zeroMinutes !== null && $startMinutes !== null ? $startMinutes - $zeroMinutes : null;
    }

    private static function toMinutes(?string $time): ?int
    {
        if ($time === null || preg_match('/^\s*(\d{1,2}):(\d{2})/', $time, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1] * 60 + (int) $matches[2];
    }
}
