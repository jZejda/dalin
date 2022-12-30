<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCredit extends Model
{
    use HasFactory;

    /**
     * App\Models\UserCredit
     *
     * @property integer $id
     * @property integer $user_id
     * @property int $user_race_profile_id
     * @property ?int $sport_event_id
     * @property float $amouth
     * @property string $currency
     * @property string $source
     * @property ?int $source_user_id
     * @property string $created_at
     * @property string $updated_at
     *
     * @property User $user
     * @property User $sourceUser
     * @property UserRaceProfile $userRaceProfile
     */

    public const CURRENCY_CZK = 'CZK';
    public const CURRENCY_EUR = 'EUR';

    public const SOURCE_CRON = 'cron';
    public const SOURCE_USER = 'user';

    public const CREDIT_TYPE_DONATION = 'donation';
    public const CREDIT_TYPE_CACHE_IN = 'in';
    public const CREDIT_TYPE_CACHE_OUT = 'out';


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
}
