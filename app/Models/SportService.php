<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportService
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int|null $oris_service_id
 * @property string|null $service_name_cz
 * @property string $last_booking_date_time
 * @property float $unit_price
 * @property int|null $qty_available
 * @property int|null $qty_already_ordered
 * @property int|null $qty_remaining
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportEvent|null $sportEvent
 * @mixin IdeHelperSportService
 */
class SportService extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_event_id',
        'oris_service_id',
        'service_name_cz',
        'sport_event_id',
        'last_booking_date_time',
        'unit_price',
        'qty_available',
        'qty_already_ordered',
        'qty_remaining',
    ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }

}
