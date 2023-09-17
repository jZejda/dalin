<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $editorial
 * @property string|null $img_url
 * @property string $content
 * @property int $content_mode
 * @property bool|null $private
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @mixin IdeHelperPost
 */
class Post extends Model
{
    use SoftDeletes;

    public const POST_PUBLIC = 0;
    public const POST_PRIVATE = 1;

    protected $casts = [
        'private' => 'boolean',
    ];

    /* @var array<string> */
    protected $fillable = [
        'title',
        'content',
        'private',
        'user_id',
        'content_mode',
        'img_url',
        'editorial',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
