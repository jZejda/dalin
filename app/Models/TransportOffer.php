<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransportOffer
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int $user_id
 * @property int $vehicle_id
 * @property string $departure_place
 * @property TransportDirection $direction
 * @property int $seats_offered
 * @property int|null $distance_km
 * @property string|null $contribution NULL = bez příspěvku
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read SportEvent|null $sportEvent
 * @property-read User|null $user
 * @property-read Vehicle|null $vehicle
 */
class TransportOffer extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'sport_event_id',
        'user_id',
        'vehicle_id',
        'departure_place',
        'direction',
        'seats_offered',
        'distance_km',
        'contribution',
        'active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'direction' => TransportDirection::class,
        'seats_offered' => 'integer',
        'distance_km' => 'integer',
        'contribution' => 'decimal:2',
        'active' => 'boolean',
    ];

    /** @return BelongsTo<SportEvent, $this> */
    public function sportEvent(): BelongsTo
    {
        return $this->belongsTo(SportEvent::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    /** @return HasMany<TransportRequest, $this> */
    public function requests(): HasMany
    {
        return $this->hasMany(TransportRequest::class);
    }

    /**
     * Počet schválených (obsazených) míst pro daný směr jízdy.
     */
    public function approvedSeatsFor(TransportDirection $direction): int
    {
        return (int) $this->requests
            ->where('status', TransportRequestStatus::Approved)
            ->filter(fn (TransportRequest $request): bool => $request->direction->overlaps($direction))
            ->sum('seats');
    }

    /**
     * Počet volných míst pro daný směr jízdy.
     */
    public function freeSeatsFor(TransportDirection $direction): int
    {
        return max(0, $this->seats_offered - $this->approvedSeatsFor($direction));
    }

    /**
     * Nejmenší počet volných míst napříč směry, které nabídka pokrývá.
     */
    public function freeSeats(): int
    {
        $freeSeats = [];

        if ($this->direction->coversThere()) {
            $freeSeats[] = $this->freeSeatsFor(TransportDirection::There);
        }
        if ($this->direction->coversBack()) {
            $freeSeats[] = $this->freeSeatsFor(TransportDirection::Back);
        }

        return $freeSeats === [] ? 0 : min($freeSeats);
    }

    /**
     * Nejvyšší počet míst, o který lze požádat v některém ze směrů.
     */
    public function maxRequestableSeats(): int
    {
        $freeSeats = [];

        if ($this->direction->coversThere()) {
            $freeSeats[] = $this->freeSeatsFor(TransportDirection::There);
        }
        if ($this->direction->coversBack()) {
            $freeSeats[] = $this->freeSeatsFor(TransportDirection::Back);
        }

        return $freeSeats === [] ? 0 : max($freeSeats);
    }

    /**
     * Scope a query to only include active offers.
     *
     * @param  Builder<TransportOffer>  $query
     * @return Builder<TransportOffer>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', true);
    }

    /**
     * Scope a query to only include offers of given sport event.
     *
     * @param  Builder<TransportOffer>  $query
     * @return Builder<TransportOffer>
     */
    public function scopeForEvent(Builder $query, int $sportEventId): Builder
    {
        return $query->where('sport_event_id', '=', $sportEventId);
    }
}
