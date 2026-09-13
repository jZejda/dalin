<?php

declare(strict_types=1);

namespace App\Http\Controllers\Demo;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class DemoScreenshotModeController
{
    private const string COOKIE_NAME = 'demo_screenshot_mode';

    public function enable(Request $request): RedirectResponse
    {
        if (config('demo.enabled') === false) {
            abort(404);
        }

        return redirect($this->redirectTarget($request))
            ->withCookie(cookie(self::COOKIE_NAME, '1', 60 * 24 * 30, httpOnly: false, sameSite: Cookie::SAMESITE_LAX));
    }

    public function disable(Request $request): RedirectResponse
    {
        if (config('demo.enabled') === false) {
            abort(404);
        }

        return redirect($this->redirectTarget($request))
            ->withCookie(cookie()->forget(self::COOKIE_NAME));
    }

    // Jen lokální cesta (žádné absolutní URL ani "//host") — parametr je z query
    // stringu, takže by jinak šel zneužít jako open redirect na cizí doménu.
    private function redirectTarget(Request $request): string
    {
        $target = $request->query('redirect', '/admin');

        if (! is_string($target) || ! str_starts_with($target, '/') || str_starts_with($target, '//')) {
            return '/admin';
        }

        return $target;
    }
}
