<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

/**
 * The parts of ORIS `getEvent` that change around the race day: where to pay and what is already published.
 */
readonly class EventOverview
{
    public function __construct(
        public ?string $bankAccount,
        public int $startsCount,
        public int $resultsCount,
    ) {
    }

    /**
     * @param array<string, mixed> $event `Data` of the ORIS getEvent response
     */
    public static function fromOris(array $event): self
    {
        $bankAccount = trim((string) ($event['EntryBankAccount'] ?? ''));

        return new self(
            bankAccount: $bankAccount !== '' ? $bankAccount : null,
            startsCount: self::count($event['CurrentStartsCount'] ?? 0),
            resultsCount: self::count($event['CurrentResultsCount'] ?? 0),
        );
    }

    private static function count(mixed $value): int
    {
        return is_numeric($value) ? max((int) $value, 0) : 0;
    }
}
