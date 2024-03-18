<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserParamType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserSetting
 *
 * @property int $id
 * @property int $user_id
 * @property UserParamType $type
 * @property array|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
class UserParam extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'type',
        'options',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'array',
        'type' => UserParamType::class,
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
