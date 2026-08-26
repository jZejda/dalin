<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MarketOrderStatus;
use App\Enums\MarketPaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\MarketProduct
 *
 * @property int $id
 * @property int $market_offer_id
 * @property string $name
 * @property string|null $description
 * @property string|null $url
 * @property float $unit_price
 * @property MarketPaymentMethod $payment_method
 * @property int|null $qty_available
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MarketOffer|null $marketOffer
 * @property-read Collection<int, MarketOrder> $orders
 */
class MarketProduct extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const string MEDIA_COLLECTION_IMAGE = 'market_product_image';

    /** @var list<string> */
    protected $fillable = [
        'market_offer_id',
        'name',
        'description',
        'url',
        'unit_price',
        'payment_method',
        'qty_available',
    ];

    /** @var array<string, string|class-string> */
    protected $casts = [
        'unit_price' => 'float',
        'payment_method' => MarketPaymentMethod::class,
        'qty_available' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGE)
            ->useDisk('public')
            ->singleFile();
    }

    public function marketOffer(): BelongsTo
    {
        return $this->belongsTo(MarketOffer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketOrder::class, 'market_product_id', 'id');
    }

    public function hasUnlimitedQty(): bool
    {
        return $this->qty_available === null;
    }

    /**
     * Total quantity allocated by orders (cancelled orders release their allocation).
     */
    public function qtyOrdered(): int
    {
        return (int) $this->orders()
            ->where('status', '!=', MarketOrderStatus::Cancelled)
            ->sum('qty');
    }

    /**
     * Remaining quantity, null when unlimited.
     */
    public function qtyRemaining(): ?int
    {
        if ($this->qty_available === null) {
            return null;
        }

        return max(0, $this->qty_available - $this->qtyOrdered());
    }

    public function isSoldOut(): bool
    {
        return $this->qtyRemaining() === 0;
    }

    public function isFree(): bool
    {
        return $this->unit_price === 0.0;
    }
}
