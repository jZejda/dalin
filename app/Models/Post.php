<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\RichContentBlocks;
use App\Services\Seo\PostSeo;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RalphJSmit\Laravel\SEO\Models\SEO;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Override;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $editorial
 * @property string|null $img_url
 * @property string $content
 * @property ContentFormat $content_mode
 * @property PostStatus|null $private
 * @property Carbon|null $news_mailed_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read SEO $seo
 */
#[Fillable([
    'title',
    'content',
    'private',
    'user_id',
    'content_mode',
    'img_url',
    'editorial',
])]
class Post extends Model implements HasMedia, HasRichContent
{
    use SoftDeletes;
    use InteractsWithMedia;
    use InteractsWithRichContent;
    use HasSEO;

    public const string MEDIA_COLLECTION_COVER = 'post_cover';
    public const string MEDIA_COLLECTION_GALLERY = 'post_gallery';
    public const int GALLERY_MAX_FILES = 40;
    public const string MEDIA_COLLECTION_CONTENT_ATTACHMENTS = 'content-file-attachments';

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'private' => PostStatus::class,
            'content_mode' => ContentFormat::class,
            'news_mailed_at' => 'datetime',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Cover image URL for the given media conversion ('card' 4:3 crop for news cards, 'detail' uncropped for the detail page).
     * An uploaded cover wins; otherwise an absolute img_url (e.g. set via API) is used as is.
     * Legacy relative img_url paths ("media/2022/06/thubnails/a.png") point to files lost in the
     * old-site migration, so they are ignored and the caller renders its placeholder instead.
     */
    public function coverUrl(string $conversion = 'card'): ?string
    {
        $media = $this->getFirstMedia(self::MEDIA_COLLECTION_COVER);

        if ($media !== null) {
            return $media->getAvailableUrl([$conversion]);
        }

        $path = trim((string) $this->img_url);

        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : null;
    }

    /**
     * Gallery photos in the order set in the admin, as thumbnail + lightbox URLs.
     *
     * @return list<array{thumb: string, full: string, alt: string}>
     */
    public function galleryImages(): array
    {
        $images = [];

        foreach ($this->getMedia(self::MEDIA_COLLECTION_GALLERY)->values() as $index => $media) {
            $images[] = [
                'thumb' => $media->getAvailableUrl(['card']),
                'full' => $media->getAvailableUrl(['detail']),
                'alt' => (string) __('content.post.public.gallery_photo_alt', ['title' => $this->title, 'number' => $index + 1]),
            ];
        }

        return $images;
    }

    /**
     * Public URL with a title slug (/novinka/12-jarni-soustredeni). The id alone identifies the
     * post, so a renamed title keeps old links working via a redirect to the current slug.
     */
    public function publicUrl(): string
    {
        return route('posts.show', ['post' => $this->routeSlug()]);
    }

    public function routeSlug(): string
    {
        $slug = Str::slug(Str::limit($this->title, 80, ''));

        return $slug !== '' ? $this->id.'-'.$slug : (string) $this->id;
    }

    /**
     * Search/social metadata for laravel-seo; manual values from the admin SEO section win.
     */
    public function getDynamicSEOData(): SEOData
    {
        return app(PostSeo::class)->dynamicData($this);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_COVER)
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();

        // Photos from a race or a training; whole race albums stay on Rajče (limited disk on shared hosting)
        $this->addMediaCollection(self::MEDIA_COLLECTION_GALLERY)
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection(self::MEDIA_COLLECTION_CONTENT_ATTACHMENTS)
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Non-queued: shared hosting sites don't run a queue worker.
        // The gallery reuses 'card' as its thumbnail and 'detail' for the lightbox.
        $this->addMediaConversion('card')
            ->performOnCollections(self::MEDIA_COLLECTION_COVER, self::MEDIA_COLLECTION_GALLERY)
            ->nonQueued()
            ->fit(Fit::Crop, 800, 600)
            ->format('jpg');

        // Detail page shows the whole photo, so only downscale and keep the original aspect ratio
        $this->addMediaConversion('detail')
            ->performOnCollections(self::MEDIA_COLLECTION_COVER, self::MEDIA_COLLECTION_GALLERY)
            ->nonQueued()
            ->fit(Fit::Max, 1600, 1600)
            ->format('jpg');

        // Social sharing preview (Open Graph / Twitter large card ratio)
        $this->addMediaConversion(PostSeo::IMAGE_CONVERSION)
            ->performOnCollections(self::MEDIA_COLLECTION_COVER)
            ->nonQueued()
            ->fit(Fit::Crop, PostSeo::IMAGE_WIDTH, PostSeo::IMAGE_HEIGHT)
            ->format('jpg');
    }

    /**
     * TipTap content (content_mode = 3): images pasted into the editor live in a Spatie collection
     * of the post, so they are deleted together with it. Markdown/HTML posts never touch this.
     */
    protected function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->json()
            ->customBlocks(RichContentBlocks::all())
            ->fileAttachmentProvider(
                SpatieMediaLibraryFileAttachmentProvider::make()
                    ->collection(self::MEDIA_COLLECTION_CONTENT_ATTACHMENTS)
                    ->mediaName(fn (TemporaryUploadedFile $file): string => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
            )
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public');
    }

    /**
     * Rewrites a Markdown/HTML post into a TipTap document (one-way, used by the admin action).
     */
    public function convertToTipTap(): void
    {
        if ($this->content_mode === ContentFormat::TipTapJson) {
            return;
        }

        $html = $this->content_mode === ContentFormat::Markdown ? Str::markdown($this->content) : $this->content;
        $document = RichContentRenderer::make($html)->customBlocks(RichContentBlocks::all())->toArray();

        $this->content = json_encode($document, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $this->content_mode = ContentFormat::TipTapJson;
        $this->save();
    }

    /**
     * Renderer for TipTap content with the post's attachments and custom blocks resolved.
     */
    public function richContentRenderer(): RichContentRenderer
    {
        /** @var RichContentAttribute $attribute */
        $attribute = $this->getRichContentAttribute('content');

        return $attribute->getRenderer();
    }
}
