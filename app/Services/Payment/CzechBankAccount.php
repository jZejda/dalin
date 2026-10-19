<?php

declare(strict_types=1);

namespace App\Services\Payment;

/**
 * Czech domestic bank account number ([prefix-]number/bank code) and its IBAN form.
 */
final readonly class CzechBankAccount
{
    private function __construct(
        public string $prefix,
        public string $number,
        public string $bankCode,
    ) {
    }

    /**
     * Parses "19-2000145399/0800" or "2901296231/2010"; spaces are ignored. Null for anything else.
     */
    public static function parse(string $account): ?self
    {
        $account = (string) preg_replace('/\s+/', '', $account);
        if (preg_match('~^(?:(\d{1,6})-)?(\d{2,10})/(\d{4})$~', $account, $m) !== 1) {
            return null;
        }

        return new self($m[1], $m[2], $m[3]);
    }

    public function toIban(): string
    {
        $bban = $this->bankCode.str_pad($this->prefix, 6, '0', STR_PAD_LEFT).str_pad($this->number, 10, '0', STR_PAD_LEFT);
        // ISO 13616: BBAN + country code as digits (C=12, Z=35) + "00", check digits = 98 - mod 97
        $checkDigits = 98 - self::mod97($bban.'123500');

        return 'CZ'.str_pad((string) $checkDigits, 2, '0', STR_PAD_LEFT).$bban;
    }

    /**
     * Mod 97 of a long digit string without bcmath (not available on every hosting).
     */
    private static function mod97(string $digits): int
    {
        $remainder = 0;
        foreach (str_split($digits, 7) as $chunk) {
            $remainder = (int) ($remainder.$chunk) % 97;
        }

        return $remainder;
    }
}
