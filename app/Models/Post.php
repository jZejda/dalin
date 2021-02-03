<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Post
 */
class Post extends Model
{
    /**
     * Fillable fields.
     *
     * @var array
     **/
    protected $fillable = [
        'title',
        'content',
        'private',
        'user_id',
        'content_mode',
        'img_url',
        'editorial',
        //'slug'

    ];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }
}
