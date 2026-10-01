<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Enums\PostStatus;
use App\Models\Page;
use App\Models\Post;
use App\Models\SportEvent;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap as SpatieSitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * sitemap.xml of the public frontend: homepage, news, open pages and recent events.
 * Cached, and dropped whenever one of the listed models is saved or deleted (bulk
 * query-builder updates, e.g. from the ORIS sync, still wait for the TTL).
 */
final class Sitemap
{
    public const string CACHE_KEY = 'seo.sitemap';

    private const int CACHE_TTL_SECONDS = 3600;

    /** Past events older than this are left out — their detail pages have little search value. */
    private const int EVENT_HISTORY_DAYS = 365;

    /** @var list<class-string<\Illuminate\Database\Eloquent\Model>> */
    private const array LISTED_MODELS = [Post::class, Page::class, SportEvent::class];

    public static function flushOnContentChanges(): void
    {
        foreach (self::LISTED_MODELS as $model) {
            $model::saved(static fn (): bool => Cache::forget(self::CACHE_KEY));
            $model::deleted(static fn (): bool => Cache::forget(self::CACHE_KEY));
        }
    }

    public function xml(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn (): string => $this->build()->render());
    }

    private function build(): SpatieSitemap
    {
        $sitemap = SpatieSitemap::create()
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
