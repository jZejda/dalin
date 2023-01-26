<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $sport_event_id
 * @property ?int $oris_service_id
 * @property string $service_name_cz
 * @property string $last_booking_date_time
 * @property float $unit_price
 * @property int $qty_available
 * @property int $qty_already_ordered
 * @property int $qty_remaining
 * @property string $created_at
 * @property string $updated_at
 *
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
