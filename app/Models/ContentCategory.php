<?php

declare(strict_types=1);

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ContentCategory
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $slug
 */
class ContentCategory extends Model
{
    /**
     * Fillable fields.
     *
     * @var array<string>
     **/
    protected $fillable = [
        'title',
        'description',
        'slug',
    ];


    /**
     * Returns Pages with specific category
     *
     * @return HasMany
     */
    public function page(): HasMany
    {
        return $this->hasMany(Page::class, 'content_category_id', 'id');
    }
}
