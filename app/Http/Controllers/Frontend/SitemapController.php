<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Seo\Sitemap;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(Sitemap $sitemap): Response
    {
        abort_unless((bool) config('site-config.features.public_site.use_public_site', true), 404);

        return response($sitemap->xml(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
