<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Services\Seo\PageSeo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use RalphJSmit\Laravel\SEO\Models\SEO;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Page
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $content_category_id
 * @property string $title
 * @property string|null $slug
 * @property string $content
 * @property ContentFormat $content_format
 * @property string $picture_attachment
 * @property string $status
 * @property int $weight
 * @property bool $page_menu
 * @property array|null $meta Legacy key/value meta, superseded by the `seo` relation (kept as a backup)
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ContentCategory|null $content_category
 * @property-read User|null $user
 * @property-read SEO $seo
 */
#[Fillable([
    'title',
    'content_category_id',
    'content',
    'status',
    'slug',
    'user_id',
    'page_menu',
    'content_format',
    'picture_attachment',
    'weight',
    'meta',
])]
class Page extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasSEO;

    public const string STATUS_OPEN = 'open';
    public const string STATUS_CLOSED = 'close';
    public const string STATUS_DRAFT = 'draft';
    public const string STATUS_ARCHIVE = 'archive';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'page_menu' => 'boolean',
            'status' => PageStatus::class,
            'content_format' => ContentFormat::class,
            'meta' => 'array',
            // content is handled by custom accessor/mutator
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasOne<ContentCategory, $this>
     */
    public function contentCategory(): HasOne
    {
        return $this->hasOne(ContentCategory::class, 'id', 'content_category_id');
    }

    public function publicUrl(): string
    {
        return url('/stranka/'.$this->slug);
    }

    /**
     * Search/social metadata for laravel-seo; manual values from the admin SEO section win.
     */
    public function getDynamicSEOData(): SEOData
    {
        return app(PageSeo::class)->dynamicData($this);
    }

    /**
     * Manually entered SEO title/description, as exposed by the API and MCP `meta` field.
     *
     * @return array<string, string>|null
     */
    public function seoMeta(): ?array
    {
        $meta = array_filter([
            'title' => $this->seo->title,
            'description' => $this->seo->description,
        ], static fn (mixed $value): bool => is_string($value) && trim($value) !== '');

        return $meta !== [] ? $meta : null;
    }

    /**
     * Get the content attribute with intelligent casting based on content_format
     */
    public function getContentAttribute(array|string|null $value): string|array|null
    {
        // If content_format is HTML, try to decode as JSON, fallback to string
        if ($this->content_format === ContentFormat::TipTapJson) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                return $decoded !== null ? $decoded : $value;
            }
            return $value;
        }

        // For Markdown, always return as string
        return $value;
    }

    /**
     * Set the content attribute with intelligent encoding based on content_format
     */
    public function setContentAttribute(array|string|null $value): void
    {
        // If value is array, encode as JSON (for RichEditor)
        if (is_array($value)) {
            $this->attributes['content'] = json_encode($value);
        } else {
            // For string values, store as string (for MarkdownEditor)
            $this->attributes['content'] = $value;
        }
    }

    /**
     * Register media collections for RichEditor attachments
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('rich-editor-attachments')
            ->useDisk('rich-editor-attachments')
            ->singleFile();
    }
}
