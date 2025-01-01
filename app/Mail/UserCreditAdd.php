<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserCreditAdd extends Mailable
{
    use Queueable;

    private int|float $amount;
    private string $currency;
    private User $user;

    public function __construct(int|float $amount, string $currency, User $user)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Mimořádný členský vklad',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.userCreditAdd',
            with: [
                'amount' => $this->amount,
                'curency' => $this->currency,
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
