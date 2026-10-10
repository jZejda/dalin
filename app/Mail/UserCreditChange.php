<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\UserParamType;
use App\Models\User;
use App\Models\UserCredit;
use App\Services\Mail\MailMoney;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * A movement on the member's club account (e.g. a paired bank payment) with the new balance.
 */
class UserCreditChange extends Mailable
{
    use Queueable;
    use SerializesModels;

    private const string DATE_TIME_FORMAT = 'j. n. Y · H:i';

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public function __construct(
        private readonly User $user,
        private readonly UserCredit $userCredit,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' - '.__('mail/user-credit-change.subject.userCreditChange'),
        );
    }

    public function content(): Content
    {
        $credit = $this->userCredit;
        $amount = (float) $credit->amount;
        $currency = $credit->currency !== '' ? $credit->currency : 'CZK';

        $facts = [
            ['icon' => $amount < 0 ? 'minus' : 'plus', 'label' => __('mail/user-credit-change.club.amount_label'), 'value' => MailMoney::format($amount, $currency, signed: true)],
            ['icon' => 'calendar-days', 'label' => __('mail/user-credit-change.club.date_label'), 'value' => (string) $credit->created_at?->format(self::DATE_TIME_FORMAT)],
            ['icon' => 'hash', 'label' => __('mail/user-credit-change.club.transaction_id_label'), 'value' => (string) $credit->id],
        ];

        // A manual movement has no bank transaction; don't show an empty reference
        if ($credit->bank_transaction_id !== null) {
            $facts[] = ['icon' => 'landmark', 'label' => __('mail/user-credit-change.club.bank_transaction_id_label'), 'value' => (string) $credit->bank_transaction_id];
        }

        return new Content(
            markdown: 'emails.event.userCreditChange',
            with: [
                'isDebit' => $amount < 0,
                'amount' => MailMoney::format(abs($amount), $currency),
                'balance' => MailMoney::format($this->user->getParam(UserParamType::UserActualBalance) ?? 0.0, $currency),
                'balanceDate' => Carbon::now()->format(self::DATE_TIME_FORMAT),
                'facts' => array_values(array_filter($facts, static fn (array $fact): bool => $fact['value'] !== '')),
                'contactEmail' => config('site-config.club.technical_email'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
