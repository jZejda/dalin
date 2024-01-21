<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ContentCategory|null $content_category
 * @property-read User|null $user
 */

class Page extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'close';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ARCHIVE = 'archive';

    protected $casts = [
        'page_menu' => 'boolean',
        'status' => PageStatus::class,
        'content_format' => ContentFormat::class,
    ];


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
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function contentCategory(): HasOne
    {
        return $this->hasOne(ContentCategory::class, 'id', 'content_category_id');
    }
}
