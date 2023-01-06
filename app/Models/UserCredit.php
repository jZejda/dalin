<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\UserCredit
 *
 * @property integer $id
 * @property integer $user_id
 * @property ?int $user_race_profile_id
 * @property ?int $sport_event_id
 * @property float $amount
 * @property string $currency
 * @property string $source
 * @property ?int $source_user_id
 * @property string $credit_type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property User $sourceUser
 * @property-read UserRaceProfile $userRaceProfile
 */

class UserCredit extends Model
{
    use HasFactory;

    public const CURRENCY_CZK = 'CZK';
    public const CURRENCY_EUR = 'EUR';

    public const SOURCE_CRON = 'cron';
    public const SOURCE_USER = 'user';

    public const CREDIT_TYPE_DONATION = 'donation';
    public const CREDIT_TYPE_CACHE_IN = 'in';
    public const CREDIT_TYPE_CACHE_OUT = 'out';


    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'user_race_profile_id',
        'sport_event_id',
        'amount',
        'currency',
        'source',
        'source_user_id',
        'credit_type',
    ];


    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userRaceProfile(): HasOne
    {
        return $this->hasOne(UserRaceProfile::class, 'id', 'user_race_profile_id');
    }

    public function sourceUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'source_user_id');
    }

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }
}
