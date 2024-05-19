<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * App\Models\SportList
     *
     * @property int $id
     * @property string $name
     * @property string $code
     * @property string $currency
     * @property string $api_url
     * @property array|null $account_credentials
     * @property string $last_synced
     * @property bool $active
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     */


    /** @var array<string, string> */
    protected $casts = [
        'active' => 'boolean',
        'last_synced' => 'datetime:Y-m-d H:i:s',
        'account_credentials' => 'array',
    ];

}
