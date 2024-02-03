<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $payer_variable_symbol
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $user_identification
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read Collection<int, UserCredit> $userCredits
 * @property-read int|null $user_credits_count
 * @property-read Collection<int, UserRaceProfile> $userRaceProfiles
 * @property-read int|null $user_race_profiles_count
 * @property-read UserSetting|null $userSetting
 */

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    //use HasPanelShield;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_EVENT_MASTER = 'event_master';
    public const ROLE_MEMBER = 'member';
    public const ROLE_REDACTOR = 'redactor';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'payer_variable_symbol'
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userRaceProfiles(): HasMany
    {
        return $this->hasMany(UserRaceProfile::class, 'user_id', 'id');
    }

    public function userCredits(): HasMany
    {
        return $this->hasMany(UserCredit::class, 'user_id', 'id');
    }

    public function getUserIdentificationAttribute(): string
    {
        return "{$this->name}  ({$this->email})";
    }

    public function userSetting(): HasOne
    {
        return $this->hasOne(UserSetting::class, 'user_id', 'id');
    }

    public function getUserOptions(): array
    {
        /** @var UserSetting $userSetting */
        $userSetting = $this->userSetting()->first();

        if (!is_null($userSetting) && !is_null($userSetting->options)) {
            return $userSetting->options;
        } else {
            return [];
        }
    }

    public function getUserRaceProfilesIds(?User $user): \Illuminate\Support\Collection
    {
        return UserRaceProfile::query()
            ->where('user_id', '=', $user->id)
            ->pluck('id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
