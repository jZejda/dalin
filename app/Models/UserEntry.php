<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EntryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserEntry
 *
 * @property int $id
 * @property ?int $oris_entry_id
 * @property int $sport_event_id
 * @property int $class_definition_id
 * @property int $user_race_profile_id
 * @property string|null $class_name
 * @property string|null $note
 * @property string|null $club_note
 * @property int|null $requested_start
 * @property int|null $si
 * @property bool $rent_si
 * @property int $stage_x
 * @property Carbon|null $entry_created
 * @property EntryStatus $entry_status
 * @property bool $entry_lock
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read SportEvent $sportEvent
 * @property-read SportClassDefinition $sportClassDefinition
 * @property-read UserRaceProfile $userRaceProfile
 */
class UserEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'oris_entry_id',
        'sport_event_id',
        'class_definition_id',
        'user_race_profile_id',
        'class_name',
        'note',
        'club_note',
        'requested_start',
        'si',
        'rent_si',
        'stage_x',
        'entry_lock',
        'entry_created',
        'entry_status',
    ];

    protected $casts = [
        'entry_created' => 'datetime:Y-m-d H:i:s',
        'rent_si' => 'bool',
        'entry_lock' => 'bool',
        'entry_status' => EntryStatus::class,
    ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }

    public function sportClassDefinition(): HasOne
    {
        return $this->hasOne(SportClassDefinition::class, 'id', 'class_definition_id');
    }

    public function userRaceProfile(): HasOne
    {
        return $this->hasOne(UserRaceProfile::class, 'id', 'user_race_profile_id');
    }
}
