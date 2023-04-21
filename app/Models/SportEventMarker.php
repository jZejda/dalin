<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SportEventMarkerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SportEventMarker
 *
 * @property int $id
 * @property int $sport_event_id
 * @property string $label
 * @property string|null $desc
 * @property float $lat
 * @property float $lon
 * @property string $type
 *
 * @property-read SportEvent $sportEvent
 */
class SportEventMarker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sport_event_id',
        'label',
        'desc',
        'lat',
        'lon',
        'type',
    ];

    protected $casts = [
        'type' => SportEventMarkerType::class,
    ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }
}
