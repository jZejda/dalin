<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BankConnector;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Models\BankAccount
 *
 * @property int $id
 * @property string $name
 * @property BankConnector $code
 * @property string $currency
 * @property array<string, string>|null $account_credentials
 * @property Carbon|null $last_synced
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BankAccount extends Model
{
    use HasFactory;

    public const string MONETA_MONEY_BANK = 'monetaMoneyBank';
    public const string FIO_BANK = 'fioBank';

    /** @var list<string> */
    protected $fillable = [
        'name',
        'code',
        'currency',
        'account_credentials',
        'last_synced',
        'active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'code' => BankConnector::class,
        'active' => 'boolean',
        'last_synced' => 'datetime:Y-m-d H:i:s',
        'account_credentials' => 'encrypted:array',
    ];

}
