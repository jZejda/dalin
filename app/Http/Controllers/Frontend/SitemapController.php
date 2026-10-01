<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\SportEvent;
use DateTimeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public const string CACHE_KEY = 'seo.sitemap';

    private const int CACHE_TTL_SECONDS = 3600;

    /** Past events older than this are left out — their detail pages have little search value. */
    private const int EVENT_HISTORY_DAYS = 365;

    public function __invoke(): Response
    {
        abort_unless((bool) config('site-config.features.public_site.use_public_site', true), 404);

        $xml = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn (): string => $this->build()->render());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function build(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(url('/')))
            ->add(Url::create(route('posts.index')));

        Post::query()
            ->where('private', PostStatus::Public)
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'created_at', 'updated_at'])
            ->each(fn (Post $post) => $sitemap->add(
                $this->url($post->publicUrl(), $post->updated_at ?? $post->created_at)
            ));

        Page::query()
            ->where('status', Page::STATUS_OPEN)
            ->whereNotNull('slug')
            ->orderBy('slug')
            ->get(['id', 'slug', 'updated_at'])
            ->each(fn (Page $page) => $sitemap->add(
                $this->url($page->publicUrl(), $page->updated_at)
            ));

        SportEvent::query()
            ->where('date', '>=', now()->subDays(self::EVENT_HISTORY_DAYS))
            ->orderBy('date')
            ->get(['id', 'updated_at'])
            ->each(fn (SportEvent $event) => $sitemap->add(
                $this->url(route('sport-event.show', $event->id), $event->updated_at)
            ));

        return $sitemap;
    }

    private function url(string $location, ?DateTimeInterface $lastModified): Url
    {
        $url = Url::create($location);

        if ($lastModified !== null) {
            $url->setLastModificationDate($lastModified);
        }

        return $url;
    }
}
