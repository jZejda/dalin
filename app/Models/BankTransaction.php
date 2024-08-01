<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Bank\Enums\TransactionIndicator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SportList
 *
 * @property int $id
 * @property int $bank_account_id
 * @property TransactionIndicator $transaction_indicator
 * @property Carbon $date
 * @property int|float $amount
 * @property string $currency
 * @property string $external_key
 * @property string|null $variable_symbol
 * @property string|null $specific_symbol
 * @property string|null $constant_symbol
 * @property string|null $description
 * @property string|null $note
 * @property string|null $error
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BankAccount|null $bankAccount
 */
class BankTransaction extends Model
{
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
        'transaction_indicator' => TransactionIndicator::class,
    ];

    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_account_id');
    }

    public function userCredit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'credit_user_id', 'id');
    }
}
