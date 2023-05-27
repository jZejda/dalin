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
 * @property ?int $external_key
 * @property ?string $letter
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
        'external_key',
        'sport_event_id',
        'letter',
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
