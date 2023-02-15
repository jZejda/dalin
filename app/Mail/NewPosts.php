<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPosts extends Mailable
{
    use Queueable;use SerializesModels;

    private User $user;
    private array $options;

    public function __construct(User $user, array $options)
    {
        $this->user = $user;
        $this->options = $options;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Novinky',
        );
    }

    public function content(): Content
    {
        $mailContent = Post::wherein('private', $this->options)->get();

        return new Content(
            markdown: 'emails.site.newPosts',
            with: [
                'type' => 'private',
                'postContent' => $mailContent
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
