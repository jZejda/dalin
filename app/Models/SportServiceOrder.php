<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportServiceOrder
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int $sport_service_id
 * @property int $user_id
 * @property int $user_race_profile_id
 * @property int $sport_service_payment_date_id
 * @property int $qty
 * @property float $unit_price
 * @property string|null $note
 * @property int|null $oris_service_entry_id
 * @property ServiceOrderStatus $status
 * @property int $source_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportEvent|null $sportEvent
 * @property-read SportService|null $sportService
 * @property-read User|null $user
 * @property-read UserRaceProfile|null $userRaceProfile
 * @property-read SportServicePaymentDate|null $paymentDate
 * @property-read User|null $sourceUser
 * @property-read UserCredit|null $userCredit
 */
class SportServiceOrder extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'sport_event_id',
        'sport_service_id',
        'user_id',
        'user_race_profile_id',
        'sport_service_payment_date_id',
        'qty',
        'unit_price',
        'note',
        'oris_service_entry_id',
        'status',
        'source_user_id',
    ];

    /** @var array<string, string|class-string> */
    protected $casts = [
        'status' => ServiceOrderStatus::class,
        'unit_price' => 'float',
        'qty' => 'integer',
    ];

    public function sportEvent(): BelongsTo
    {
        return $this->belongsTo(SportEvent::class);
    }

    public function sportService(): BelongsTo
    {
        return $this->belongsTo(SportService::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userRaceProfile(): BelongsTo
    {
        return $this->belongsTo(UserRaceProfile::class);
    }

    public function paymentDate(): BelongsTo
    {
        return $this->belongsTo(SportServicePaymentDate::class, 'sport_service_payment_date_id');
    }

    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function userCredit(): HasOne
    {
        return $this->hasOne(UserCredit::class, 'sport_service_order_id', 'id');
    }

    public function totalAmount(): float
    {
        return $this->qty * $this->unit_price;
    }
}
