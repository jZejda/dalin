<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MarketOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\MarketOrder
 *
 * @property int $id
 * @property int $market_product_id
 * @property int $user_id
 * @property int $qty
 * @property float $unit_price
 * @property string|null $note
 * @property MarketOrderStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MarketProduct|null $marketProduct
 * @property-read User|null $user
 * @property-read UserCredit|null $userCredit
 */
class MarketOrder extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'market_product_id',
        'user_id',
        'qty',
        'unit_price',
        'note',
        'status',
    ];

    /** @var array<string, string|class-string> */
    protected $casts = [
        'status' => MarketOrderStatus::class,
        'unit_price' => 'float',
        'qty' => 'integer',
    ];

    public function marketProduct(): BelongsTo
    {
        return $this->belongsTo(MarketProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userCredit(): HasOne
    {
        return $this->hasOne(UserCredit::class, 'market_order_id', 'id');
    }

    public function totalAmount(): float
    {
        return $this->qty * $this->unit_price;
    }
}
