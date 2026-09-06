<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read string $initials
 * @property-read User|null $user
 * @property-read Collection<int, UserEntry> $userEntries
 * @property-read Collection<int, UserCredit> $userCredits
 */
class UserRaceProfile extends Model
{
    use HasFactory;

    /** @var list<string> */
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
        'street',
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

    public function getInitialsAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->first_name, 0, 1).mb_substr($this->last_name, 0, 1));
    }

    public function userEntries(): HasMany
    {
        return $this->hasMany(UserEntry::class, 'user_race_profile_id', 'id');
    }

    public function userCredits(): HasMany
    {
        return $this->hasMany(UserCredit::class, 'user_race_profile_id', 'id');
    }
}
