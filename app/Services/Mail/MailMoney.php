<?php

declare(strict_types=1);

namespace App\Services\Mail;

/**
 * Money amounts as shown in the club mails: "2 000 Kč", "−320 Kč", "+2 000 Kč" (cs)
 * or "2,000 CZK" (en). Decimals only when the amount has any. Plain PHP formatting,
 * so it works on hosts without the intl extension.
 */
final class MailMoney
{
    private const string NBSP = "\u{00A0}";

    private const string MINUS = "\u{2212}";

    public static function format(float $amount, string $currency = 'CZK', bool $signed = false): string
    {
        $czech = app()->getLocale() === 'cs';
        $absolute = abs($amount);
        $decimals = round($absolute, 2) === floor($absolute) ? 0 : 2;

        $number = $czech
            ? number_format($absolute, $decimals, ',', self::NBSP)
            : number_format($absolute, $decimals, '.', ',');

        $sign = match (true) {
            $amount < 0 => self::MINUS,
            $signed && $amount > 0 => '+',
            default => '',
        };

        $symbol = $czech && $currency === 'CZK' ? 'Kč' : $currency;

        return $sign.$number.self::NBSP.$symbol;
    }
}
