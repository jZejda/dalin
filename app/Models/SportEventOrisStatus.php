<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrisPaymentState;
use App\Services\Payment\QrPayment;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Override;

/**
 * Snapshot of what ORIS knows about an event that matters to the club: the organizer's bank account,
 * whether the start list / results are published (getEvent) and the club's entry-fee settlement
 * (getEventBalance). Refreshed by OrisEventStatusRefresher.
 *
 * @property int $id
 * @property int $sport_event_id
 * @property string|null $bank_account
 * @property int $starts_count
 * @property int $results_count
 * @property float|null $fee_entry
 * @property float|null $fee_si
 * @property float|null $fee_service
 * @property float|null $fee_total
 * @property float|null $paid
 * @property float|null $to_be_paid
 * @property string $currency
 * @property string|null $payment_vs
 * @property Carbon $fetched_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportEvent $sportEvent
 */
#[Fillable([
    'sport_event_id',
    'bank_account',
    'starts_count',
    'results_count',
    'fee_entry',
    'fee_si',
    'fee_service',
    'fee_total',
    'paid',
    'to_be_paid',
    'currency',
    'payment_vs',
    'fetched_at',
])]
class SportEventOrisStatus extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'starts_count' => 'integer',
            'results_count' => 'integer',
            'fee_entry' => 'float',
            'fee_si' => 'float',
            'fee_service' => 'float',
            'fee_total' => 'float',
            'paid' => 'float',
            'to_be_paid' => 'float',
            'fetched_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<SportEvent, $this> */
    public function sportEvent(): BelongsTo
    {
        return $this->belongsTo(SportEvent::class);
    }

    /**
     * Whether ORIS has a settlement row for the club (false = the club has no entries in the event).
     */
    public function hasClubBalance(): bool
    {
        return $this->fee_total !== null;
    }

    public function paymentState(): ?OrisPaymentState
    {
        if (! $this->hasClubBalance()) {
            return null;
        }

        return OrisPaymentState::fromAmounts($this->paid ?? 0.0, $this->to_be_paid ?? 0.0);
    }

    public function hasStartList(): bool
    {
        return $this->starts_count > 0;
    }

    public function hasResults(): bool
    {
        return $this->results_count > 0;
    }

    /**
     * QR payment of the outstanding amount to the organizer's account; null when nothing is due
     * or the account is not a Czech domestic one.
     */
    public function qrPayment(): ?QrPayment
    {
        if ($this->bank_account === null || $this->paymentState()?->isOutstanding() !== true) {
            return null;
        }

        // Payment note for the organizer's bank statement — data, not UI text, so not localized
        $message = 'Startovne '.config('site-config.club.abbr').' '.$this->sportEvent->name;

        return QrPayment::forCzechAccount(
            $this->bank_account,
            (float) $this->to_be_paid,
            $this->currency,
            $this->payment_vs,
            $message,
        );
    }
}
