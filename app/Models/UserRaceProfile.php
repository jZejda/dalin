<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserRaceProfile
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int $user_id
 * @property string $reg_number
 * @property string|null $oris_id
 * @property string|null $club_user_id
 * @property string|null $email
 * @property string|null $phone
 * @property string $gender
 * @property int|null $si
 * @property int|null $iof_id
 * @property string|null $city
 * @property string|null $street
 * @property string|null $zip
 * @property string|null $licence_ob
 * @property string|null $licence_lob
 * @property string|null $licence_mtbo
 * @property Carbon|null $active_until
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $user_race_full_name
 * @property-read User|null $user
 */
class UserRaceProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'reg_number',
        'oris_id',
        'club_user_id',
        'iof_id',
        'email',
        'phone',
        'gender',
        'city',
        'zip',
        'si',
        'licence_ob',
        'licence_lob',
        'licence_mtbo',
        'active_until',
        'active',
    ];

    protected $casts = [
        'active_until' => 'date',
        'active' => 'boolean',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getUserRaceFullNameAttribute(): string
    {
        return "{$this->reg_number} - {$this->first_name} {$this->last_name}";
    }

}
