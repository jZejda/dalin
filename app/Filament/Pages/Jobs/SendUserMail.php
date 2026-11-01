<?php

declare(strict_types=1);

namespace App\Filament\Pages\Jobs;

use App\Mail\UserAppNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class SendUserMail
{
    /** Pseudo-target selecting every active user regardless of their roles (incl. users without a role). */
    public const string TARGET_ALL = 'all';

    /**
     * @param array<int, string> $targetUsers role names (AppRoles values) and/or TARGET_ALL
     * @param User|null $sender who wrote the message; shown as the sender in the e-mail
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $content,
        private readonly ?string $replyTo,
        private readonly array $targetUsers,
        private readonly ?User $sender = null,
    ) {
    }

    /**
     * Active users matching the selected target groups; inactive accounts never get the e-mail.
     *
     * @return Builder<User>
     */
    public function recipientsQuery(): Builder
    {
        $query = User::query()->where('active', '=', 1);

        if (in_array(self::TARGET_ALL, $this->targetUsers, true)) {
            return $query;
        }

        return $query->whereHas('roles', function (Builder $q): void {
            $q->whereIn('name', $this->targetUsers);
        });
    }

    /**
     * Queues the e-mail to every recipient.
     *
     * @return int number of queued e-mails
     */
    public function send(): int
    {
        $users = $this->recipientsQuery()->get();

        foreach ($users as $user) {
            Mail::to($user)
                ->queue(new UserAppNotification(
                    $this->sender,
                    $this->subject,
                    $this->content,
                    $this->replyTo,
                ));
        }

        Log::channel('site')->info('E-mail Users notifikace', [
            'target_users' => $this->targetUsers,
            'recipients' => $users->count(),
        ]);

        return $users->count();
    }
}
