<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\MailSource;
use App\Models\MailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Context;
use Symfony\Component\Mime\Address;

class LogSentMail
{
    /**
     * Persist a record for every e-mail that leaves the application.
     *
     * The source (user / cron / system) is resolved from the request Context,
     * which Laravel automatically propagates into queued jobs — so a mail sent
     * via ->queue() keeps the source captured at dispatch time, even though it
     * is physically delivered later by the queue worker.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        $recipients = array_map(
            fn (Address $address): string => $address->getAddress(),
            $message->getTo(),
        );

        /** @var array{type: string, user_id?: int|null}|null $source */
        $source = Context::get('mail_source');

        $sourceType = $source['type'] ?? (app()->runningInConsole() ? MailSource::Cron->value : MailSource::System->value);

        MailLog::create([
            'recipient' => implode(', ', $recipients),
            'subject' => $message->getSubject(),
            'mailable' => $event->data['__laravel_mailable'] ?? null,
            'source_type' => $sourceType,
            'source_user_id' => $source['user_id'] ?? null,
        ]);
    }
}
