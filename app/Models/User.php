<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $password
 * @property ?string $remember_token
 * @property ?string $email_verified_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read string $user_identification
 */

class User extends Authenticatable
{
    use HasApiTokens;use HasFactory;use Notifiable;
    use HasRoles;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_EVENT_MASTER = 'event_master';
    public const ROLE_MEMBER = 'member';
    public const ROLE_REDACTOR = 'redactor';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'password',
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
}
