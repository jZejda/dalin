<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserCreditStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserCredit
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $user_race_profile_id
 * @property int|null $sport_event_id
 * @property int|null $sport_service_id
 * @property int|null $oris_balance_id
 * @property UserCreditStatus $status
 * @property float $amount
 * @property float|null $balance
 * @property string $currency
 * @property string $source
 * @property int|null $source_user_id
 * @property string $credit_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read User|null $sourceUser
 * @property-read SportEvent|null $sportEvent
 * @property-read SportService|null $sportService
 * @property-read User|null $user
 * @property-read Collection<int, UserCreditNote> $userCreditNotes
 * @property-read int|null $user_credit_notes_count
 * @property-read UserRaceProfile|null $userRaceProfile
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
