<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SportEvent;
use App\Models\User;
use Filament\Notifications\Notification;

final class NotificationService
{
    private string $title;

    private string $body;

    /** @var User[] */
    private array $recipients;

    public function __construct(string $title, string $body, array $recipients)
    {
        $this->title = $title;
        $this->body = $body;
        $this->recipients = $recipients;
    }

    protected function create(): void
    {
        /** @var User $recipient */
        $recipient = auth()->user();

        /** @var SportEvent $sportEvent */
        //$sportEvent = $this->record;

        Notification::make()
            ->title($this->title)
            ->body('Uživatel: '.$recipient->name.' | Název: '.$sportEvent->name)
            ->sendToDatabase($this->recipients);

    }
}
