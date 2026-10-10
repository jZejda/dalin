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
use Illuminate\Support\Collection;

/**
 * Digest of the news articles from the last days, by the member's notification settings.
 */
class NewPosts extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /**
     * @param Collection<int, Post> $postContent
     */
    public function __construct(
        private readonly Collection $postContent,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' - '.__('mail/new-posts.subject.new_posts'),
        );
    }

    public function content(): Content
    {
        $articles = $this->postContent->map(static function (Post $post): array {
            $html = MailRichContent::html($post->content, $post->content_mode);

            return ['title' => $post->title, 'html' => $html, 'text' => MailRichContent::text($html)];
        })->values()->all();

        return new Content(
            markdown: 'emails.site.newPosts',
            with: [
                'articles' => $articles,
                'settingsUrl' => UserMailNotification::getUrl(panel: 'admin'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
