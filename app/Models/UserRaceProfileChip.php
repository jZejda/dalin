<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiChipSport;
use App\Enums\SiChipType;
use Database\Factories\UserRaceProfileChipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Additional SI chip of a race profile (the primary chip lives in user_race_profiles.si).
 *
 * @property int $id
 * @property int $user_race_profile_id
 * @property int $si
 * @property SiChipSport $sport
 * @property SiChipType $type
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read UserRaceProfile $userRaceProfile
 */
#[Fillable(['user_race_profile_id', 'si', 'sport', 'type', 'sort_order'])]
class UserRaceProfileChip extends Model
{
    /** @use HasFactory<UserRaceProfileChipFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'si' => 'integer',
            'sport' => SiChipSport::class,
            'type' => SiChipType::class,
        ];
    }

    /**
     * @return BelongsTo<UserRaceProfile, $this>
     */
    public function userRaceProfile(): BelongsTo
    {
        return $this->belongsTo(UserRaceProfile::class);
    }
}
