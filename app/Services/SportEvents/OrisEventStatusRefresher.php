<?php

declare(strict_types=1);

namespace App\Services\SportEvents;

use App\Models\SportEvent;
use App\Models\SportEventOrisStatus;
use App\Services\OrisApiService;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Keeps SportEventOrisStatus in sync with ORIS: getEvent (bank account, start list / results counts)
 * and getEventBalance (club's entry-fee settlement).
 */
final readonly class OrisEventStatusRefresher
{
    /** A stored status older than this is refreshed on the next page view or cron run. */
    public const int STALE_AFTER_MINUTES = 60;

    public function __construct(private OrisApiService $oris = new OrisApiService())
    {
    }

    /**
     * Loads the current status from ORIS and stores it.
     *
     * @throws HttpClientException when ORIS is unreachable or does not answer with status OK
     * @throws InvalidArgumentException when the event is not linked to ORIS
     */
    public function refresh(SportEvent $sportEvent): SportEventOrisStatus
    {
        $orisId = $sportEvent->oris_id;
        if ($orisId === null) {
            throw new InvalidArgumentException('Sport event '.$sportEvent->id.' has no ORIS ID.');
        }

        $overview = $this->oris->getEventOverview($orisId);
        $balance = $this->oris->getEventClubBalance($orisId, (string) config('site-config.club.abbr'));

        $status = SportEventOrisStatus::query()->updateOrCreate(
            ['sport_event_id' => $sportEvent->id],
            [
                'bank_account' => $overview->bankAccount,
                'starts_count' => $overview->startsCount,
                'results_count' => $overview->resultsCount,
                'fee_entry' => $balance?->feeEntry,
                'fee_si' => $balance?->feeSi,
                'fee_service' => $balance?->feeService,
                'fee_total' => $balance?->feeTotal,
                'paid' => $balance?->paid,
                'to_be_paid' => $balance?->toBePaid,
                'currency' => $balance->currency ?? 'CZK',
                'payment_vs' => $balance?->paymentVs,
                'fetched_at' => now(),
            ],
        );
        $sportEvent->setRelation('orisStatus', $status);

        return $status;
    }

    /**
     * Refreshes the status when it is missing or stale; on an ORIS failure keeps (and returns) the stored one.
     */
    public function refreshIfStale(SportEvent $sportEvent): ?SportEventOrisStatus
    {
        $status = $sportEvent->orisStatus;
        if ($sportEvent->oris_id === null || ($status !== null && ! self::isStale($status))) {
            return $status;
        }

        return $this->refreshQuietly($sportEvent);
    }

    /**
     * Like refresh(), but an ORIS failure is only logged and the stored status (if any) is returned.
     */
    public function refreshQuietly(SportEvent $sportEvent): ?SportEventOrisStatus
    {
        // Demo events carry fictitious ORIS IDs and seeded statuses; ORIS would only answer "ID not valid"
        if (config('demo.enabled')) {
            return $sportEvent->orisStatus;
        }

        try {
            return $this->refresh($sportEvent);
        } catch (HttpClientException $e) {
            Log::channel('site')->warning('ORIS event status refresh failed for event '.$sportEvent->id.': '.$e->getMessage());

            return $sportEvent->orisStatus;
        }
    }

    public static function isStale(SportEventOrisStatus $status): bool
    {
        return $status->fetched_at->lt(now()->subMinutes(self::STALE_AFTER_MINUTES));
    }
}
