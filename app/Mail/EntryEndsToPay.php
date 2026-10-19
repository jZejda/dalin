<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\ListsSportEventDeadlines;
use App\Models\SportEvent;
use App\Models\SportEventOrisStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Services\Mail\MailBranding;
use App\Services\Mail\MailMoney;

/**
 * Report for billing specialists: races whose given entry deadline (1–3) is ending
 * right now and have entered members, so the entry fees can be paid. Races linked to ORIS
 * carry the club's outstanding amount, the organizer's account and a QR payment
 * from the stored SportEventOrisStatus (refreshed by the cron job right before sending).
 */
class EntryEndsToPay extends Mailable
{
    use ListsSportEventDeadlines;
    use Queueable;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /**
     * @param Collection<int, SportEvent> $sportEvents
     */
    public function __construct(
        private readonly Collection $sportEvents,
        private readonly int $deadline,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: MailBranding::subject(__('mail/entry-ends-to-pay.subject.entry_ends_to_pay', ['term' => $this->termOrdinal()])),
        );
    }

    public function content(): Content
    {
        $column = 'entry_date_'.$this->deadline;
        $events = $this->sportEvents
            ->filter(static fn (SportEvent $event): bool => $event->{$column} instanceof Carbon)
            ->values();

        // deadlineRows() keeps the order of $events, all of which pass its deadline filter
        $rows = array_map(
            static fn (array $row, SportEvent $event): array => $row + ['payment' => self::paymentRow($event)],
            $this->deadlineRows($events, $column, static fn (SportEvent $event): array => [self::orisLabel($event)]),
            $events->all(),
        );

        return new Content(
            markdown: 'emails.event.entryEndsToPay',
            with: [
                'term' => $this->termOrdinal(),
                'termTitle' => __('mail/common.club_layout.terms.'.$this->deadline),
                'events' => $rows,
                'hasPayments' => array_any($rows, static fn (array $row): bool => $row['payment'] !== null),
            ]
        );
    }

    /**
     * @return array{outstanding: bool, amount: string, breakdown: string, bank_account: string|null, vs: string|null, fetched_at: string, qr_png: string|null, qr_name: string}|null
     */
    private static function paymentRow(SportEvent $event): ?array
    {
        $status = $event->oris_id !== null ? $event->orisStatus : null;
        $state = $status?->paymentState();
        if (! $status instanceof SportEventOrisStatus || $state === null) {
            return null;
        }

        $money = static fn (?float $amount): string => MailMoney::format((float) $amount, $status->currency);

        return [
            'outstanding' => $state->isOutstanding(),
            'amount' => $state->isOutstanding() ? $money($status->to_be_paid) : $money($status->paid),
            'breakdown' => __('mail/entry-ends-to-pay.payment.breakdown', [
                'entry' => $money($status->fee_entry),
                'si' => $money($status->fee_si),
                'service' => $money($status->fee_service),
                'paid' => $money($status->paid),
            ]),
            'bank_account' => $status->bank_account,
            'vs' => $status->payment_vs,
            'fetched_at' => $status->fetched_at->format('j. n. · H:i'),
            'qr_png' => $status->qrPayment()?->toPng(),
            'qr_name' => 'qr-platba-'.$event->id.'.png',
        ];
    }

    public function attachments(): array
    {
        return [];
    }

    private function termOrdinal(): string
    {
        return __('mail/common.club_layout.term_ordinals.'.$this->deadline);
    }
}
