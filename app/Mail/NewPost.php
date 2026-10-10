<?php

declare(strict_types=1);

namespace App\Mail;

use App\Filament\Clusters\Other\Pages\UserMailNotification;
use App\Models\Post;
use App\Services\Mail\MailRichContent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\Mail\MailBranding;

/**
 * A single news article sent from the admin panel.
 */
class NewPost extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public $subject;

    public function __construct(
        private readonly Post $post,
        ?string $subject,
    ) {
        $this->subject = $subject ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: MailBranding::subject(__('mail/new-post.subject.new_post').($this->subject !== '' ? ' – '.$this->subject : '')),
        );
    }

    public function content(): Content
    {
        $html = MailRichContent::html($this->post->content, $this->post->content_mode);

        return new Content(
            markdown: 'emails.site.newPost',
            with: [
                'article' => ['title' => $this->post->title, 'html' => $html, 'text' => MailRichContent::text($html)],
                'settingsUrl' => UserMailNotification::getUrl(panel: 'admin'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
