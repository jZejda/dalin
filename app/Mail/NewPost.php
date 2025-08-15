<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPost extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Post $post;
    public $subject;

    public function __construct(Post $post, ?string $subject)
    {
        $this->post = $post;
        $this->subject = $subject ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Novinky' . ($this->subject !== null ? ' - ' . $this->subject : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.site.newPost',
            with: [
                'post' => $this->post
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
