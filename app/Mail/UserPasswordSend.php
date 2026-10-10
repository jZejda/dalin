<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use App\Shared\Helpers\AppHelper;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Login details for a new account or after a password reset (the password is generated
 * by the app and sent in the mail, as the login flow works today).
 */
class UserPasswordSend extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const string ACTION_SEND_PASSWORD = 'sendPassword';
    public const string ACTION_RESET_PASSWORD = 'resetPassword';

    /** Help page the secondary link points to. */
    private const string HELP_PAGE = 'ovladani-aplikace';

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public function __construct(
        private readonly string $password,
        private readonly User $user,
        private readonly string $action = self::ACTION_SEND_PASSWORD,
    ) {
    }

    public function envelope(): Envelope
    {
        if ($this->action === self::ACTION_SEND_PASSWORD) {
            $actionSubject = __('mail/user-password-send.subject.send_password');
        } else {
            $actionSubject = __('mail/user-password-send.subject.reset_password');
        }

        return new Envelope(
            subject: config('app.name').' - '.$actionSubject,
        );
    }

    public function content(): Content
    {
        $loginUrl = (string) Filament::getPanel('admin')->getLoginUrl();

        return new Content(
            markdown: 'emails.user.userSendPassword',
            with: [
                'variant' => $this->action === self::ACTION_SEND_PASSWORD ? 'new_account' : 'reset',
                'facts' => [
                    ['icon' => 'user', 'label' => __('mail/user-password-send.club.name_label'), 'value' => $this->user->name],
                    ['icon' => 'mail', 'label' => __('mail/user-password-send.club.email_label'), 'value' => $this->user->email],
                    ['icon' => 'key-round', 'label' => __('mail/user-password-send.club.password_label'), 'value' => $this->password, 'wide' => true],
                    ['icon' => 'globe', 'label' => __('mail/user-password-send.club.url_label'), 'value' => (string) preg_replace('#^https?://#', '', $loginUrl), 'wide' => true],
                ],
                'loginUrl' => $loginUrl,
                'helpUrl' => AppHelper::getPageHelpUrl(self::HELP_PAGE),
                'contactEmail' => config('site-config.club.technical_email'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
