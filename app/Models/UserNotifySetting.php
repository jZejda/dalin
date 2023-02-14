<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\UserNotifySetting
 *
 * @property integer $id
 * @property integer $user_id
 * @property ?string $type
 * @property ?array $options
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read User $user
 */
class UserNotifySetting extends Model
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
        'options' => 'array',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
