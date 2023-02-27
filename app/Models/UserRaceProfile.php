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
 * @property string|null $oris_id
 * @property string|null $email
 * @property string|null $phone
 * @property string $gender
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read string $user_race_full_name
 * @property-read User|null $user
 */
class UserRaceProfile extends Model
{
    use HasFactory;

    /**
     * App\Models\UserRaceProfile
     *
     * @property int $id
     * @property string $first_name
     * @property string $last_name
     * @property int $user_id
     * @property string $reg_number
     * @property ?int $oris_id
     * @property ?string $email
     * @property ?string $phone
     * @property string $gender
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

    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'reg_number',
        'oris_id',
        'email',
        'phone',
        'gender',
        'city',
        'zip',
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
