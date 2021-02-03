<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

/**
 * App\Page
 */
class Page extends Model
{
    use Sluggable, HasFactory;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable():array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    /**
     * Fillable fields.
     *
     * @var array
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
        'weight',

    ];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }
}
