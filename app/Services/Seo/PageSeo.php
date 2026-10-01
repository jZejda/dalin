<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Enums\ContentFormat;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\RichContentBlocks;
use App\Models\Page;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
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
            title: $this->manualValue($page, 'title') ?? $pageTitle,
            description: $this->manualValue($page, 'description') ?? $this->excerpt($page),
            url: $url,
            schema: SchemaCollection::make()
                ->add(fn (): array => $this->siteSeo->breadcrumbSchema($this->breadcrumbs($page, $pageTitle, $url))),
        );
    }

    public function excerpt(Page $page): ?string
    {
        $content = $page->content;

        $html = match ($page->content_format) {
            ContentFormat::Html => is_string($content) ? $content : '',
            ContentFormat::Markdown => is_string($content) ? Str::markdown($content) : '',
            // A malformed TipTap document must not take the whole page down with it
            ContentFormat::TipTapJson => (string) rescue(
                static fn (): string => RichContentRenderer::make($content)
                    ->customBlocks(RichContentBlocks::all())
                    ->toText(),
                '',
                report: false,
            ),
        };

        return $this->siteSeo->description($html);
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
     * Mirrors the visible breadcrumb: an event's pages sit under the event detail;
     * a plain category has no page of its own, so it is left out.
     *
     * @return list<array{name: string, url: string}>
     */
    private function breadcrumbs(Page $page, string $pageTitle, string $url): array
    {
        $items = [['name' => __('content.post.public.breadcrumb_home'), 'url' => url('/')]];

        $sportEvent = $page->contentCategory?->sportEvent;

        if ($sportEvent !== null) {
            $items[] = ['name' => $sportEvent->name, 'url' => route('sport-event.show', $sportEvent->id)];
        }

        $items[] = ['name' => $pageTitle, 'url' => $url];

        return $items;
    }

    private function manualValue(Page $page, string $attribute): ?string
    {
        $value = $page->seo->getAttribute($attribute);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
