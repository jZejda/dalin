<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Enums\ContentFormat;
use App\Models\Page;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Search/social metadata of a content page (/stranka/{slug}): description from the content
 * in any of its formats and BreadcrumbList JSON-LD.
 */
final class PageSeo
{
    public function __construct(private readonly SiteSeo $siteSeo)
    {
    }

    /**
     * laravel-seo prefers these values over the page's `seo` row, so manual values
     * from the admin SEO section are resolved here first.
     */
    public function dynamicData(Page $page): SEOData
    {
        $pageTitle = $this->plainTitle($page);
        $url = $page->publicUrl();

        return new SEOData(
            title: $this->siteSeo->manualValue($page, 'title') ?? $pageTitle,
            description: $this->siteSeo->manualValue($page, 'description') ?? $this->excerpt($page),
            url: $url,
            schema: SchemaCollection::make()
                ->add(fn (): array => $this->siteSeo->breadcrumbSchema($this->breadcrumbs($page, $pageTitle, $url))),
        );
    }

    public function excerpt(Page $page): ?string
    {
        return $this->siteSeo->description($this->siteSeo->contentHtml($page->content_format, $page->content));
    }

    /**
     * Page titles are rendered as HTML or Markdown on the frontend, so markup is stripped here.
     */
    private function plainTitle(Page $page): string
    {
        $title = $page->content_format === ContentFormat::Markdown
            ? Str::markdown($page->title)
            : $page->title;

        return $this->siteSeo->plainText($title) ?? $page->title;
    }

    /**
     * Mirrors the visible breadcrumb below the homepage: an event's pages sit under the
     * event detail; a plain category has no page of its own, so it is left out.
     *
     * @return list<array{name: string, url: string}>
     */
    private function breadcrumbs(Page $page, string $pageTitle, string $url): array
    {
        $items = [];
        $sportEvent = $page->contentCategory?->sportEvent;

        if ($sportEvent !== null) {
            $items[] = ['name' => $sportEvent->name, 'url' => route('sport-event.show', $sportEvent->id)];
        }

        $items[] = ['name' => $pageTitle, 'url' => $url];

        return $items;
    }
}
