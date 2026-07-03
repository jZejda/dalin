<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransportRequest
 *
 * @property int $id
 * @property int $transport_offer_id
 * @property int $user_id
 * @property TransportDirection $direction
 * @property int $seats
 * @property TransportRequestStatus $status
 * @property Carbon|null $approved_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TransportOffer|null $transportOffer
 * @property-read User|null $user
 */
class TransportRequest extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'transport_offer_id',
        'user_id',
        'direction',
        'seats',
        'status',
        'approved_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'direction' => TransportDirection::class,
        'seats' => 'integer',
        'status' => TransportRequestStatus::class,
        'approved_at' => 'datetime',
    ];

    /** @return BelongsTo<TransportOffer, $this> */
    public function transportOffer(): BelongsTo
    {
        return $this->belongsTo(TransportOffer::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === TransportRequestStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === TransportRequestStatus::Approved;
    }

    /**
     * Scope a query to only include approved requests.
     *
     * @param  Builder<TransportRequest>  $query
     * @return Builder<TransportRequest>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', '=', TransportRequestStatus::Approved->value);
    }

    /**
     * Scope a query to only include pending requests.
     *
     * @param  Builder<TransportRequest>  $query
     * @return Builder<TransportRequest>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', '=', TransportRequestStatus::Pending->value);
    }
}
