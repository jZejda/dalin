<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\ContentCategory
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $slug
 * @property int|null $sport_event_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Page> $page
 * @property-read SportEvent|null $sportEvent
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
        'sport_event_id',
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

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }
}
