<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use App\Services\Mail\MailMoney;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;

/**
 * Monthly report for billing specialists: members whose credit balance is negative.
 */
class UsersInDebit extends Mailable
{
    private const string DATE_TIME_FORMAT = 'j. n. Y · H:i';

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' - '.__('mail/users-in-debit.subject.users_in_debit'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.userInDebit',
            with: [
                'date' => Carbon::now()->format(self::DATE_TIME_FORMAT),
                'debtors' => $this->debtors(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * @return list<array{name: string, email: string, amount: string}>
     */
    private function debtors(): array
    {
        return array_values(User::query()
            ->withSum('userCredits', 'amount')
            ->orderBy('id')
            ->get()
            ->filter(static fn (User $user): bool => (float) $user->getAttribute('user_credits_sum_amount') < 0)
            ->map(static fn (User $user): array => [
                'name' => $user->name,
                'email' => $user->email,
                'amount' => MailMoney::format((float) $user->getAttribute('user_credits_sum_amount')),
            ])
            ->all());
    }
}
