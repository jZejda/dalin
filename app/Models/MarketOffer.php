<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MarketOfferStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * App\Models\MarketOffer
 *
 * @property int $id
 * @property int $user_id
 * @property bool $is_club_offer
 * @property string $title
 * @property string|null $description
 * @property MarketOfferStatus $status
 * @property Carbon $closes_at
 * @property Carbon|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read Collection<int, MarketProduct> $products
 * @property-read Collection<int, MarketOrder> $orders
 */
class MarketOffer extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'is_club_offer',
        'title',
        'description',
        'status',
        'closes_at',
        'closed_at',
    ];

    /** @var array<string, string|class-string> */
    protected $casts = [
        'is_club_offer' => 'boolean',
        'status' => MarketOfferStatus::class,
        'closes_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(MarketProduct::class, 'market_offer_id', 'id');
    }

    /** @return HasManyThrough<MarketOrder, MarketProduct, $this> */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            MarketOrder::class,
            MarketProduct::class,
            'market_offer_id',
            'market_product_id',
        );
    }

    public function isOpenForOrders(): bool
    {
        return $this->status === MarketOfferStatus::Active
            && $this->closes_at->isFuture();
    }
}
