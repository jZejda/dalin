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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $user_race_full_name
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereClubUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereIofId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereLicenceLob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereLicenceMtbo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereLicenceOb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereOrisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereRegNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRaceProfile whereZip($value)
 * @mixin \Eloquent
 * @mixin IdeHelperUserRaceProfile
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
