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
 * @property string $created_at
 * @property string $updated_at

 * @property User $user
 */
class Post extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'private' => 'boolean',
    ];

    /**
     * Fillable fields.
     *
     * @var array<string>
     **/
    protected $fillable = [
        'title',
        'content',
        'private',
        'user_id',
        'content_mode',
        'img_url',
        'editorial',
    ];

    /**
     * Returns User object for single Post
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
