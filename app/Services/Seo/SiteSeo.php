<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Enums\ContentFormat;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\RichContentBlocks;
use App\Models\AppSetting;
use App\Models\Page;
use App\Models\Post;
use Closure;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Facades\SEOManager;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\ImageMeta;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Site-wide SEO for the public frontend, built on ralphjsmit/laravel-seo.
 *
 * Club identity comes from config('site-config.club.*') (already overridden from the admin
 * panel by AppSetting::applyClubConfigOverrides()); description, sharing image, logo and
 * social profiles are edited per site on the Club settings page (AppSetting::SEO_*).
 */
final class SiteSeo
{
    public const string TITLE_SEPARATOR = ' | ';

    private const int DESCRIPTION_LENGTH = 160;

    /** @var array<string, string> */
    private const array OG_LOCALES = [
        'cs' => 'cs_CZ',
        'en' => 'en_US',
    ];

    /**
     * Registers the site defaults with the package. Everything is resolved at render time,
     * so club settings saved in the admin panel apply without depending on boot order.
     */
    public static function configure(): void
    {
        SEOManager::SEODataTransformer(static fn (SEOData $data): SEOData => app(self::class)->applyDefaults($data));
    }

    /**
     * Resolves what the layout passes to seo(): the SEO source given by the controller,
     * with the page's `@section('title')` as the title fallback.
     */
    public function forView(Model|SEOData|null $source, string $sectionTitle): Model|SEOData
    {
        if ($source instanceof Post || $source instanceof Page) {
            // laravel-seo reads the data via $model->seo->model; without this inverse relation
            // the morphTo would query the model again and getDynamicSEOData() would run on a
            // fresh copy, ignoring the controller's eager loads.
            $source->seo->setRelation('model', $source);
        }

        if ($source instanceof Model) {
            return $source;
        }

        $data = $source !== null ? clone $source : new SEOData();

        if ($data->title === null) {
            // Inline @section values are stored HTML-escaped; the tag renderer escapes again.
            $title = trim(html_entity_decode($sectionTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $data->title = $title !== '' ? $title : null;
        }

        return $data;
    }

    /**
     * Last-moment defaults applied to every page (registered as an SEOData transformer,
     * so it runs after the model / controller data has been merged).
     */
    public function applyDefaults(SEOData $data): SEOData
    {
        $clubName = self::clubName();

        // Done here rather than via config('seo.title.suffix'), which the package reads
        // before the transformers run.
        if ($clubName !== null && $data->enableTitleSuffix) {
            if ($data->title !== null) {
                $data->title .= self::TITLE_SEPARATOR.$clubName;
            }

            if ($data->openGraphTitle !== null) {
                $data->openGraphTitle .= self::TITLE_SEPARATOR.$clubName;
            }
        }

        $data->site_name ??= $clubName;
        $data->description ??= AppSetting::getSeoDescription();

        $data->image ??= $this->defaultImageUrl();

        if ($data->image !== null && $data->imageMeta === null) {
            $data->imageMeta = $this->publicDiskImageMeta($data->image);
        }

        if ($data->locale !== null && isset(self::OG_LOCALES[$data->locale])) {
            $data->locale = self::OG_LOCALES[$data->locale];
        }

        // url()->current() drops the query string, which would point every page of
        // a paginated listing at page 1 as its canonical.
        $page = request()->integer('page');
        if ($data->canonical_url === null && $page > 1 && $data->url === url()->current()) {
            $data->canonical_url = $data->url.'?page='.$page;
        }

        return $data;
    }

    /**
     * Plain text of an HTML fragment; null when nothing readable is left. Block-level tags
     * become spaces so adjacent blocks ("</p><p>") don't glue words together, inline tags
     * ("<em>") are just dropped so no stray space lands before punctuation.
     */
    public function plainText(string $html): ?string
    {
        $spaced = (string) preg_replace(
            '#<(?:/?(?:p|div|h[1-6]|li|ul|ol|dl|dt|dd|blockquote|pre|table|tr|td|th|section|article|figure|figcaption)\b[^>]*|br\s*/?|hr\s*/?)>#i',
            ' ',
            $html,
        );

        $text = Str::squish(html_entity_decode(strip_tags($spaced), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $text !== '' ? $text : null;
    }

    /**
     * HTML of a post/page body in any of its storage formats (TipTap is rendered to text).
     */
    public function contentHtml(ContentFormat $format, mixed $content): string
    {
        return match ($format) {
            ContentFormat::Html => is_string($content) ? $content : '',
            ContentFormat::Markdown => is_string($content) ? Str::markdown($content) : '',
            ContentFormat::TipTapJson => $this->richContentText($content),
        };
    }

    /**
     * Value the editor typed into the admin SEO section, or null when left empty.
     */
    public function manualValue(Post|Page $model, string $attribute): ?string
    {
        return self::filled($model->seo->getAttribute($attribute));
    }

    /**
     * Trimmed string, or null for anything blank or not a string.
     */
    public static function filled(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /**
     * Meta description from an HTML fragment, cut on a word boundary.
     */
    public function description(string $html): ?string
    {
        $text = $this->plainText($html);

        return $text !== null
            ? Str::limit($text, self::DESCRIPTION_LENGTH - 1, '…', preserveWords: true)
            : null;
    }

    public function homepage(): SEOData
    {
        return new SEOData(
            schema: SchemaCollection::make()
                ->add(fn (): array => $this->organizationSchema())
                ->add(fn (): array => $this->websiteSchema()),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function organizationSchema(): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SportsOrganization',
            '@id' => $this->homeUrl().'#organization',
            'name' => self::clubName(),
            'alternateName' => self::clubAbbr(),
            'url' => $this->homeUrl(),
            'logo' => $this->publicDiskUrl(AppSetting::getSeoLogoPath()),
            'image' => $this->defaultImageUrl(),
            'description' => AppSetting::getSeoDescription(),
            'sport' => 'Orienteering',
            'sameAs' => AppSetting::getSeoSameAs(),
        ], static fn (mixed $value): bool => $value !== null && $value !== []);
    }

    /**
     * The club as an article publisher / author (full entity, since the
     * SportsOrganization block itself is only rendered on the homepage).
     *
     * @return array<string, mixed>
     */
    public function publisherReference(): array
    {
        return array_filter([
            '@type' => 'SportsOrganization',
            '@id' => $this->homeUrl().'#organization',
            'name' => self::clubName(),
            'url' => $this->homeUrl(),
            'logo' => $this->publicDiskUrl(AppSetting::getSeoLogoPath()),
        ], static fn (mixed $value): bool => $value !== null);
    }

    /**
     * @param  list<array{name: string, url: string}>  $items  below the homepage, down to the current page
     * @return array<string, mixed>
     */
    public function breadcrumbSchema(array $items): array
    {
        $items = [['name' => __('app.seo.breadcrumb_home'), 'url' => url('/')], ...$items];
        $elements = [];

        foreach ($items as $index => $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function websiteSchema(): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => $this->homeUrl().'#website',
            'name' => self::clubName(),
            'alternateName' => self::clubAbbr(),
            'url' => $this->homeUrl(),
            'inLanguage' => app()->getLocale(),
            'publisher' => ['@id' => $this->homeUrl().'#organization'],
        ], static fn (mixed $value): bool => $value !== null);
    }

    /**
     * The club's default sharing image (Club settings), if uploaded.
     */
    public function defaultImageUrl(): ?string
    {
        return $this->publicDiskUrl(AppSetting::getSeoImagePath());
    }

    private static function clubName(): ?string
    {
        return self::filled(config('site-config.club.full_name'));
    }

    private static function clubAbbr(): ?string
    {
        return self::filled(config('site-config.club.abbr'));
    }

    /**
     * A TipTap document as escaped plain text (a malformed one must not take the page down).
     */
    private function richContentText(mixed $content): string
    {
        $document = is_string($content) ? json_decode($content, true) : $content;

        if (! is_array($document)) {
            return '';
        }

        return e((string) rescue(
            static fn (): string => RichContentRenderer::make($document)
                ->customBlocks(RichContentBlocks::all())
                ->toText(),
            '',
            report: false,
        ));
    }

    /**
     * Per-request memo: the package runs the transformer twice per render (once from its
     * constructor), and the disk checks / image decoding are the same both times. Request
     * attributes keep it scoped to one request, tests included.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    private function remember(string $key, Closure $callback): mixed
    {
        $attributes = request()->attributes;
        $key = 'site-seo.'.$key;

        if (! $attributes->has($key)) {
            $attributes->set($key, $callback());
        }

        return $attributes->get($key);
    }

    private function homeUrl(): string
    {
        return rtrim(url('/'), '/').'/';
    }

    private function publicDiskUrl(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        return $this->remember('url.'.$path, static fn (): ?string => Storage::disk('public')->exists($path)
            ? url(Storage::disk('public')->url($path))
            : null);
    }

    /**
     * The package only measures images under public_path(); uploads (settings, Media
     * Library covers) live on the `public` disk, so for its URLs the dimensions
     * (og:image:width/height, Twitter card type) are read here.
     */
    private function publicDiskImageMeta(string $url): ?ImageMeta
    {
        return $this->remember('image-meta.'.$url, fn (): ?ImageMeta => $this->measurePublicDiskImage($url));
    }

    private function measurePublicDiskImage(string $url): ?ImageMeta
    {
        $disk = Storage::disk('public');
        $baseUrl = rtrim(url($disk->url('')), '/').'/';

        if (! Str::startsWith($url, $baseUrl)) {
            return null;
        }

        $path = rawurldecode(Str::after($url, $baseUrl));

        if (Str::contains($path, '..') || ! $disk->exists($path)) {
            return null;
        }

        $size = @getimagesize($disk->path($path));

        if ($size === false) {
            return null;
        }

        // A URL makes the constructor skip its own public_path() lookup.
        $meta = new ImageMeta($url);
        $meta->width = $size[0];
        $meta->height = $size[1];

        return $meta;
    }
}
