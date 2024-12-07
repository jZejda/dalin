<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserParamType;
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
 * @property string|null $payer_variable_symbol
 * @property bool $active
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $user_identification
 * @property-read string $user_identification_billing
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

    public const string ROLE_SUPER_ADMIN = 'super_admin';
    public const string ROLE_EVENT_MASTER = 'event_master';
    public const string ROLE_MEMBER = 'member';
    public const string ROLE_REDACTOR = 'redactor';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'payer_variable_symbol',
        'active',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
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

    public function getUserIdentificationBillingAttribute(): string
    {
        $userVs = $this->payer_variable_symbol ?? '---';
        return "{$this->name} - VS: {$userVs}  ({$this->email})";
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
            ->where('user_id', '=', $user?->id)
            ->pluck('id');
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function canCreateEntry(): bool
    {
        $userCreditLimit = config('site-config.club.user_credit_limit');
        if ($this->getParam(UserParamType::UserActualBalance) > $userCreditLimit) {
            return true;
        }

        return false;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        //return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        if ($this->isActive()) {
            return true;
        }

        return false;
    }

    public function setParam(UserParamType $paramType, mixed $value): void
    {
        $userParam = UserParam::query()
            ->where('user_id', '=', $this->id)
            ->where('type', '=', $paramType)
            ->first();

        if ($userParam === null) {
            $userParam = new UserParam();
            $userParam->type = $paramType;
            $userParam->user_id = $this->id;
        }

        $userParam->value = $value;
        $userParam->saveOrFail();
    }

    public function getParam(UserParamType $paramType): mixed
    {
        /** @var UserParam $userParam */
        $userParam = UserParam::query()
            ->where('user_id', '=', $this->id)
            ->where('type', '=', $paramType)
            ->first();

        if ($userParam !== null) {
            return match ($userParam->type) {
                UserParamType::UserActualBalance => floatval($userParam->value),
            };
        }

        return null;
    }
}
