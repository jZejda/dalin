<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Enums\ContentFormat;
use App\Models\Post;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Search/social metadata of a news post: description from the editorial or the content,
 * the cover cropped for sharing, NewsArticle + BreadcrumbList JSON-LD.
 */
final class PostSeo
{
    /** Media conversion of the post cover used for og:image / twitter:image */
    public const string IMAGE_CONVERSION = 'og';

    public const int IMAGE_WIDTH = 1200;

    public const int IMAGE_HEIGHT = 630;

    /** Google truncates longer NewsArticle headlines */
    private const int HEADLINE_LENGTH = 110;

    public function __construct(private readonly SiteSeo $siteSeo)
    {
    }

    /**
     * laravel-seo prefers these values over the post's `seo` row, so manual values
     * from the admin SEO section are resolved here first.
     */
    public function dynamicData(Post $post): SEOData
    {
        $title = $this->manualValue($post, 'title') ?? $post->title;
        $description = $this->manualValue($post, 'description') ?? $this->excerpt($post);
        $image = $this->imageUrl($post);
        $url = $post->publicUrl();

        return new SEOData(
            title: $title,
            description: $description,
            author: $post->user?->name,
            image: $image,
            url: $url,
            published_time: $post->created_at,
            modified_time: $post->updated_at,
            type: 'article',
            schema: SchemaCollection::make()
                ->add(fn (): array => $this->newsArticleSchema($post, $title, $description, $image, $url))
                ->add(fn (): array => $this->siteSeo->breadcrumbSchema([
                    ['name' => __('content.post.public.breadcrumb_home'), 'url' => url('/')],
                    ['name' => __('content.post.public.breadcrumb_news'), 'url' => route('posts.index')],
                    ['name' => $post->title, 'url' => $url],
                ])),
        );
    }

    /**
     * Plain-text summary: the editorial if filled in, otherwise the start of the content.
     */
    public function excerpt(Post $post): ?string
    {
        $editorial = trim((string) $post->editorial);

        $html = match (true) {
            $editorial !== '' => Str::markdown($editorial),
            $post->content_mode === ContentFormat::Markdown => Str::markdown($post->content),
            $post->content_mode === ContentFormat::Html => $post->content,
            // TipTap JSON would need the rich-content renderer; the club description is used instead
            default => '',
        };

        return $this->siteSeo->description($html);
    }

    public function imageUrl(Post $post): ?string
    {
        $media = $post->getFirstMedia(Post::MEDIA_COLLECTION_COVER);

        if ($media !== null) {
            // Covers uploaded before the sharing conversion existed fall back to the detail image
            return url($media->getAvailableUrl([self::IMAGE_CONVERSION, 'detail']));
        }

        $legacyUrl = $post->coverUrl('detail');

        return $legacyUrl !== null ? url($legacyUrl) : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function newsArticleSchema(Post $post, string $title, ?string $description, ?string $image, string $url): array
    {
        $publisher = $this->siteSeo->publisherReference();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'headline' => Str::limit($title, self::HEADLINE_LENGTH - 1, '…', preserveWords: true),
            'description' => $description,
            'image' => $image !== null ? [$image] : null,
            'datePublished' => $post->created_at?->toIso8601String(),
            'dateModified' => ($post->updated_at ?? $post->created_at)?->toIso8601String(),
            'author' => $post->user !== null ? ['@type' => 'Person', 'name' => $post->user->name] : $publisher,
            'publisher' => $publisher,
        ], static fn (mixed $value): bool => $value !== null);
    }

    private function manualValue(Post $post, string $attribute): ?string
    {
        $value = $post->seo->getAttribute($attribute);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
