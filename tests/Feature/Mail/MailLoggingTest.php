<?php

declare(strict_types=1);

use App\Enums\MailSource;
use App\Http\Middleware\CaptureMailSource;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Context::flush();
});

/**
 * Minimal Mailable used only to exercise the MessageSent listener.
 */
class StubLoggedMail extends Mailable
{
    public function build(): self
    {
        return $this->subject('Testovací předmět')->html('<p>Ahoj</p>');
    }
}

it('logs an outgoing e-mail into the mail_logs table', function () {
    Mail::to('jan@example.com')->send(new StubLoggedMail());

    expect(MailLog::count())->toBe(1);

    $log = MailLog::firstOrFail();

    expect($log->recipient)->toBe('jan@example.com')
        ->and($log->subject)->toBe('Testovací předmět')
        ->and($log->mailable)->toBe(StubLoggedMail::class)
        ->and($log->mailable_short)->toBe('StubLoggedMail');
});

it('attributes the mail to the user captured in the context', function () {
    $user = User::factory()->create();

    Context::add('mail_source', ['type' => MailSource::User->value, 'user_id' => $user->id]);

    Mail::to('jan@example.com')->send(new StubLoggedMail());

    $log = MailLog::firstOrFail();

    expect($log->source_type)->toBe(MailSource::User)
        ->and($log->source_user_id)->toBe($user->id)
        ->and($log->sourceUser->is($user))->toBeTrue();
});

it('attributes the mail to the cron source from the context', function () {
    Context::add('mail_source', ['type' => MailSource::Cron->value]);

    Mail::to('jan@example.com')->send(new StubLoggedMail());

    $log = MailLog::firstOrFail();

    expect($log->source_type)->toBe(MailSource::Cron)
        ->and($log->source_user_id)->toBeNull();
});

it('falls back to a non-user source when no context is present', function () {
    Mail::to('jan@example.com')->send(new StubLoggedMail());

    $log = MailLog::firstOrFail();

    expect($log->source_type)->not->toBe(MailSource::User);
});

it('logs multiple recipients as a comma separated string', function () {
    Mail::to(['jan@example.com', 'eva@example.com'])->send(new StubLoggedMail());

    $log = MailLog::firstOrFail();

    expect($log->recipient)->toBe('jan@example.com, eva@example.com');
});

it('captures the system source for a guest web request', function () {
    app(CaptureMailSource::class)->handle(
        Request::create('/'),
        fn (): Response => new Response('ok'),
    );

    expect(Context::get('mail_source'))->toBe(['type' => MailSource::System->value]);
});

it('captures the user source for an authenticated web request', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app(CaptureMailSource::class)->handle(
        Request::create('/'),
        fn (): Response => new Response('ok'),
    );

    expect(Context::get('mail_source'))->toBe([
        'type' => MailSource::User->value,
        'user_id' => $user->id,
    ]);
});

it('logs a queued guest mail as system, not cron', function () {
    // Simulate the source captured during a guest web request that is then
    // restored in the (console) queue worker when the mail is delivered.
    Context::add('mail_source', ['type' => MailSource::System->value]);

    Mail::to('host@example.com')->send(new StubLoggedMail());

    $log = MailLog::firstOrFail();

    expect($log->source_type)->toBe(MailSource::System)
        ->and($log->source_user_id)->toBeNull();
});
