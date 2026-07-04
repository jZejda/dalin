<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Vehicle
 *
 * @property int $id
 * @property int|null $user_id NULL = klubové vozidlo
 * @property string $name
 * @property string|null $brand
 * @property VehicleType $type
 * @property string|null $description
 * @property int $seats
 * @property string|null $operator
 * @property string|null $consumption
 * @property string|null $price_per_km
 * @property bool $active
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 */
class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'type',
        'description',
        'seats',
        'operator',
        'consumption',
        'price_per_km',
        'active',
        'is_default',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'type' => VehicleType::class,
        'seats' => 'integer',
        'consumption' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Vehicle $vehicle): void {
            if (! $vehicle->is_default) {
                return;
            }

            static::query()
                ->where('user_id', $vehicle->user_id)
                ->when($vehicle->exists, fn (Builder $query): Builder => $query->whereKeyNot($vehicle->getKey()))
                ->update(['is_default' => false]);
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isClubVehicle(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Scope a query to only include club vehicles.
     *
     * @param  Builder<Vehicle>  $query
     * @return Builder<Vehicle>
     */
    public function scopeClub(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope a query to only include vehicles owned by given user.
     *
     * @param  Builder<Vehicle>  $query
     * @return Builder<Vehicle>
     */
    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', '=', $userId);
    }

    /**
     * Scope a query to only include active vehicles.
     *
     * @param  Builder<Vehicle>  $query
     * @return Builder<Vehicle>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', true);
    }

    /**
     * Scope a query to only include the default vehicle of its owner.
     *
     * @param  Builder<Vehicle>  $query
     * @return Builder<Vehicle>
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', '=', true);
    }
}
