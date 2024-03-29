<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransportOfferDirection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransportOffer
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $sport_event_id
 * @property TransportOfferDirection $direction
 * @property int $free_seats
 * @property int $distance
 * @property float $transport_contribution
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TransportOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_event_id',
        'user_id',
        'direction',
        'free_seats',
        'distance',
        'transport_contribution',
        'description',
    ];

    protected $casts = [
        'direction' => TransportOfferDirection::class,
        ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
