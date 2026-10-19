<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

/**
 * One club's row of the ORIS `getEventBalance` settlement (amounts in the event currency).
 */
readonly class EventClubBalance
{
    public function __construct(
        public float $feeEntry,
        public float $feeSi,
        public float $feeService,
        public float $feeTotal,
        public float $paid,
        public float $toBePaid,
        public string $currency = 'CZK',
        public ?string $paymentVs = null,
    ) {
    }

    /**
     * @param array<string, mixed> $club one `Data.Clubs.Club_<id>` item of the ORIS response
     */
    public static function fromOris(array $club, string $currency): self
    {
        return new self(
            feeEntry: self::amount($club['FeeEntry'] ?? 0),
            feeSi: self::amount($club['FeeSI'] ?? 0),
            feeService: self::amount($club['FeeService'] ?? 0),
            feeTotal: self::amount($club['FeeTotal'] ?? 0),
            paid: self::amount($club['Paid'] ?? 0),
            toBePaid: self::amount($club['ToBePaid'] ?? 0),
            currency: $currency,
            paymentVs: filled($club['PaymentVS'] ?? null) ? (string) $club['PaymentVS'] : null,
        );
    }

    public function isFullyPaid(): bool
    {
        return $this->toBePaid <= 0;
    }

    private static function amount(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
