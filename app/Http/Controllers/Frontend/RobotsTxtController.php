<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Served dynamically (instead of public/robots.txt) because the sitemap URL and
 * the public-site switch differ per site.
 */
class RobotsTxtController extends Controller
{
    public function __invoke(): Response
    {
        if (! config('site-config.features.public_site.use_public_site', true)) {
            $lines = ['User-agent: *', 'Disallow: /'];
        } else {
            $lines = [
                'User-agent: *',
                'Disallow: /admin',
                'Disallow: /design-system',
                '',
                'Sitemap: '.url('/sitemap.xml'),
            ];
        }

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
