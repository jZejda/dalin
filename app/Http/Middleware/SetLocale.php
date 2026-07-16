<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolves the request locale for frontend routes. Reads the same
     * session key the panel language switch writes, so the choice made
     * in the admin panel carries over to the public site.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        /** @var mixed $locale */
        $locale = $request->session()->get('locale')
            ?? ($user instanceof \App\Models\User ? $user->locale : null)
            ?? config('app.locale');

        if (is_string($locale) && in_array($locale, ['cs', 'en'], true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
