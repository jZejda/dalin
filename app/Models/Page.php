<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
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
 * @property int $content_format
 * @property string $picture_attachment
 * @property string $status
 * @property int $weight
 * @property bool $page_menu
 * @property array|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ContentCategory|null $content_category
 * @property-read User|null $user
 */

class Page extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const string STATUS_OPEN = 'open';
    public const string STATUS_CLOSED = 'close';
    public const string STATUS_DRAFT = 'draft';
    public const string STATUS_ARCHIVE = 'archive';

    protected $casts = [
        'page_menu' => 'boolean',
        'status' => PageStatus::class,
        'content_format' => ContentFormat::class,
        'meta' => 'array',
        // content is handled by custom accessor/mutator
    ];

    /** @var list<string> */
    protected $fillable = [
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
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function contentCategory(): HasOne
    {
        return $this->hasOne(ContentCategory::class, 'id', 'content_category_id');
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
