<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Models\BankAccount
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $currency
 * @property string $api_url
 * @property array $account_credentials
 * @property Carbon|null $last_synced
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BankAccount extends Model
{
    use HasFactory;

    public const string MONETA_MONEY_BANK = 'monetaMoneyBank';

    /** @var array<string, string> */
    protected $casts = [
        'active' => 'boolean',
        'last_synced' => 'datetime:Y-m-d H:i:s',
        'account_credentials' => 'array',
    ];

}
