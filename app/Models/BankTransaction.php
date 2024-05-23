<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportList
 *
 * @property int $id
 * @property int $bank_account_id
 * @property string $type
 * @property Carbon $date
 * @property float $amount
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
    ];

    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_account_id');
    }
}
