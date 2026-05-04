<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $relay_team_id
 * @property int $slot
 * @property int|null $user_race_profile_id
 * @property int|null $user_entry_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RelayTeamMember extends Model
{
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'slot' => 'integer',
    ];

    /** @var list<string> */
    protected $fillable = [
        'relay_team_id',
        'slot',
        'user_race_profile_id',
        'user_entry_id',
    ];

    public function relayTeam(): BelongsTo
    {
        return $this->belongsTo(RelayTeam::class, 'relay_team_id', 'id');
    }

    public function userRaceProfile(): BelongsTo
    {
        return $this->belongsTo(UserRaceProfile::class, 'user_race_profile_id', 'id');
    }

    public function userEntry(): BelongsTo
    {
        return $this->belongsTo(UserEntry::class, 'user_entry_id', 'id');
    }
}
