<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\MailSource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class CaptureMailSource
{
    /**
     * Record who is responsible for any mail dispatched during this request.
     *
     * The value is stored in the request Context, which Laravel serializes into
     * queued jobs — so even mails sent via ->queue() are attributed to the right
     * source, not to the queue worker that delivers them.
     *
     * A logged-in user is attributed as "user"; any other web request (guest
     * forms such as registration or password reset) is attributed as "system".
     * The hourly cron route overrides this with "cron" inside CommonCron.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Context::add('mail_source', Auth::check()
            ? ['type' => MailSource::User->value, 'user_id' => Auth::id()]
            : ['type' => MailSource::System->value]);

        return $next($request);
    }
}
