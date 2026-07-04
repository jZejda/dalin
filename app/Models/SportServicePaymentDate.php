<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportServicePaymentDate
 *
 * @property int $id
 * @property int $sport_service_id
 * @property Carbon $payment_date
 * @property string $description
 * @property int $created_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportService|null $sportService
 * @property-read User|null $createdByUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SportServiceOrder> $serviceOrders
 */
class SportServicePaymentDate extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'sport_service_id',
        'payment_date',
        'description',
        'created_by_user_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'payment_date' => 'date',
    ];

    public function sportService(): BelongsTo
    {
        return $this->belongsTo(SportService::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(SportServiceOrder::class, 'sport_service_payment_date_id');
    }
}
