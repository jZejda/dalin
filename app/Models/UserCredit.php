<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserCreditStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\UserCredit
 *
 * @property int $id
 * @property ?int $user_id
 * @property ?int $user_race_profile_id
 * @property ?int $sport_event_id
 * @property ?int $sport_service_id
 * @property float $amount
 * @property ?float $balance
 * @property string $currency
 * @property string $source
 * @property UserCreditStatus $status
 * @property ?int $source_user_id
 * @property ?int $oris_balance_id
 * @property string $credit_type
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read int $userCreditNoteCount
 * @property-read User $user
 * @property-read User $sourceUser
 * @property-read UserRaceProfile $userRaceProfile
 * @property-read SportService $sportService
 * @property-read UserCreditNote $userCreditNote
 */

class UserCredit extends Model
{
    use HasFactory;

    public const CURRENCY_CZK = 'CZK';
    public const CURRENCY_EUR = 'EUR';

    public const SOURCE_CRON = 'cron';
    public const SOURCE_USER = 'user';

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'user_race_profile_id',
        'sport_event_id',
        'sport_service_id',
        'amount',
        'currency',
        'source',
        'status',
        'source_user_id',
        'oris_balance_id',
        'credit_type',
    ];

    /** @var array<string,string|class-string> */
    protected $casts = [
        'status' => UserCreditStatus::class,
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

    public function sportService(): HasOne
    {
        return $this->hasOne(SportService::class, 'id', 'sport_service_id');
    }

    public function userCreditNotes(): HasMany
    {
        return $this->hasMany(UserCreditNote::class, 'user_credit_id', 'id');
    }

    public function userCreditNoteCount(): int
    {
        return $this->hasMany(UserCreditNote::class, 'user_credit_id', 'id')->whereNotIn('internal', [1])->count();
    }

    public function userCreditInternalOrisNote(int $userCreditId): ?Model
    {
        return UserCreditNote::where('user_credit_id', '=', $userCreditId)
            ->where('internal', '=', 1)
            ->first();
    }
}
