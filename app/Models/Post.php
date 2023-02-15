<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $img_url
 * @property string $editorial
 * @property bool $private
 * @property int $user_id
 * @property int $content_mode
 * @property string|null $created_at
 * @property string|null $updated_at

 * @property-read User $user
 */
class Post extends Model
{
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
