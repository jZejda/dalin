<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Models\AppSetting;
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

        if ($data->image === null) {
            $imagePath = AppSetting::getSeoImagePath();

            if ($imagePath !== null && Storage::disk('public')->exists($imagePath)) {
                $data->image = url(Storage::disk('public')->url($imagePath));
            }
        }

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
            'image' => $this->publicDiskUrl(AppSetting::getSeoImagePath()),
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
     * @param  list<array{name: string, url: string}>  $items  from the homepage down to the current page
     * @return array<string, mixed>
     */
    public function breadcrumbSchema(array $items): array
    {
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

    private static function clubName(): ?string
    {
        $name = config('site-config.club.full_name');

        return is_string($name) && trim($name) !== '' ? trim($name) : null;
    }

    private static function clubAbbr(): ?string
    {
        $abbr = config('site-config.club.abbr');

        return is_string($abbr) && trim($abbr) !== '' ? trim($abbr) : null;
    }

    private function homeUrl(): string
    {
        return rtrim(url('/'), '/').'/';
    }

    private function publicDiskUrl(?string $path): ?string
    {
        if ($path === null || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return url(Storage::disk('public')->url($path));
    }

    /**
     * The package only measures images under public_path(); uploads (settings, Media
     * Library covers) live on the `public` disk, so for its URLs the dimensions
     * (og:image:width/height, Twitter card type) are read here.
     */
    private function publicDiskImageMeta(string $url): ?ImageMeta
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
