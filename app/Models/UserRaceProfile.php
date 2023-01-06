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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $user_race_full_name
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereOrisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereRegNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereUserId($value)
 * @mixin \Eloquent
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
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getUserRaceFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

}

