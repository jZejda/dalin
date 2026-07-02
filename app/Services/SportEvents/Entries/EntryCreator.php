<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Entries;

use App\Models\SportEvent;
use App\Services\SportEvents\Entries\Strategy\EntryStrategy;
use App\Services\SportEvents\Entries\Strategy\ManualEntryStrategy;
use App\Services\SportEvents\Entries\Strategy\OrisEntryStrategy;
use App\Services\SportEvents\Entries\Strategy\RelayEntryStrategy;

class EntryCreator
{
    /** @var list<EntryStrategy> */
    private array $strategies;

    public function __construct(
        RelayEntryStrategy $relay,
        OrisEntryStrategy $oris,
        ManualEntryStrategy $manual,
    ) {
        // Order matters: relay and ORIS are checked before the manual fallback.
        $this->strategies = [$relay, $oris, $manual];
    }

    /**
     * Create an entry for the given event using the appropriate strategy.
     *
     * @param array<string, mixed> $data
     */
    public function create(SportEvent $event, array $data): EntryResult
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canHandle($event)) {
                return $strategy->create($event, $data);
            }
        }

        // ManualEntryStrategy::canHandle() always returns true, so this is unreachable.
        return new EntryResult(success: false);
    }

    public static function make(): self
    {
        $persister = new EntryPersister();

        return new self(
            relay: new RelayEntryStrategy(new RelaySlotManager(), $persister),
            oris: new OrisEntryStrategy(new OrisEntryClient(), $persister),
            manual: new ManualEntryStrategy($persister),
        );
    }
}
