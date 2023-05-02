<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\UserRaceProfile
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int $user_id
 * @property string $reg_number
 * @property int|null $oris_id
 * @property int|null $club_user_id
 * @property int|null $iof_id
 * @property string|null $email
 * @property string|null $phone
 * @property string $gender
 * @property int|null $si
 * @property string $city
 * @property string $street
 * @property string $zip
 * @property string $licence_ob
 * @property string $licence_lob
 * @property string $licence_mtbo
 *
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
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
