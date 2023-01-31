<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Page
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property bool $status
 * @property string $slug
 * @property int $user_id
 * @property int $page_menu
 * @property int $content_format
 * @property int $weight
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property int|null $content_category_id
 * @property string $picture_attachment
 *
 * @property-read ContentCategory|null $content_category
 */

class Page extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'close';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ARCHIVE = 'archive';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'page_menu' => 'boolean',
    ];

    /**
     * Fillable fields.
     *
     * @var array<string>
     **/
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


    /**
     * Returns User object for single Page
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Returns ContentCategory object
     *
     * @return HasOne
     */
    public function content_category(): HasOne
    {
        return $this->hasOne(ContentCategory::class, 'id', 'content_category_id');
    }
}
