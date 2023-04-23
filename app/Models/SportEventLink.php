<?php

namespace App\Models;

use App\Enums\SportEventLinkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SportEventMarker
 *
 * @property int $id
 * @property ?int $external_key
 * @property int $sport_event_id
 * @property bool $internal
 * @property string|null $source_path
 * @property string|null $source_url
 * @property string $source_type
 * @property string|null $name_cz
 * @property string|null $name_en
 * @property string|null $description_cz
 * @property string|null $description_en
 *
 * @property-read SportEvent $sportEvent
 */
class SportEventLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_key',
        'sport_event_id',
        'internal',
        'source_path',
        'source_url',
        'source_type',
        'name_cz',
        'name_en',
        'description_cz',
        'description_en',
    ];

    protected $casts = [
        'internal' => 'bool',
        'source_type' => SportEventLinkType::class,
    ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }

}
