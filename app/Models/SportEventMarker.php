<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SportEventMarkerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportEventMarker
 *
 * @property int $id
 * @property int|null $external_key
 * @property int $sport_event_id
 * @property string|null $letter
 * @property string $label
 * @property string|null $desc
 * @property float $lat
 * @property float $lon
 * @property SportEventMarkerType|null $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read SportEvent|null $sportEvent
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
