<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NewPosts extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Collection $postContent;

    public function __construct(Collection $postContent)
    {
        $this->postContent = $postContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Novinky',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.site.newPosts',
            with: [
                'type' => 'private',
                'postContent' => $this->postContent
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
