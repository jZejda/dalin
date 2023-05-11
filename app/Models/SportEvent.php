<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SportEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportEvent
 *
 * @property int $id
 * @property string $name
 * @property string|null $alt_name
 * @property int|null $oris_id
 * @property string $date
 * @property string|null $place
 * @property array|null $region
 * @property array|null $organization
 * @property string|null $entry_desc
 * @property string|null $event_info
 * @property string|null $event_warning
 * @property int|null $sport_id
 * @property int|null $discipline_id
 * @property int|null $level_id
 * @property bool $use_oris_for_entries
 * @property bool|null $ranking
 * @property float|null $ranking_coefficient
 * @property string|null $event_type
 * @property Carbon $entry_date_1
 * @property Carbon|null $entry_date_2
 * @property Carbon|null $entry_date_3
 * @property string|null $start_time
 * @property string|null $gps_lat
 * @property string|null $gps_lon
 * @property array|null $weather
 * @property int|null $parent_id
 * @property bool $dont_update_excluded
 * @property bool $cancelled
 * @property string|null $cancelled_reason
 * @property int|null $stages
 * @property string|null $last_update
 * @property string|null $last_calculate_cost
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read Carbon|null $lastEntryDate
 *
 * @property-read string $sport_event_oris_title
 * @property-read string $sport_event_last_cost_calculate
 * @property-read SportDiscipline|null $sportDiscipline
 * @property-read SportService|null $sportServices
 * @property-read SportLevel|null $sportLevel
 * @property-read UserEntry $userEntry
 * @property-read SportEventLink $sportEventLink
 */

class SportEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alt_name',
        'oris_id',
        'date',
        'place',
        'region',
        'organization',
        'entry_desc',
        'event_info',
        'event_warning',
        'sport_id',
        'discipline_id',
        'level_id',
        'use_oris_for_entries',
        'ranking',
        'ranking_coefficient',
        'event_type',
        'entry_date_1',
        'entry_date_2',
        'entry_date_3',
        'start_time',
        'gps_lat',
        'gps_lon',
        'weather',
        'parent_id',
        'last_update',
        'last_calculate_cost',
        'cancelled',
        'cancelled_reason',
        'stages',
        'dont_update_excluded',
    ];

    protected $casts = [
        'date' => 'date',
        'use_oris_for_entries' => 'bool',
        'ranking' => 'bool',
        'region' => 'array',
        'organization' => 'array',
        'entry_date_1' => 'datetime:Y-m-d H:i:s',
        'entry_date_2' => 'datetime:Y-m-d H:i:s',
        'entry_date_3' => 'datetime:Y-m-d H:i:s',
        'last_update' => 'datetime:Y-m-d H:i:s',
        'last_calculate_cost' => 'datetime:Y-m-d H:i:s',
        'cancelled' => 'bool',
        'meta' => 'array',
        'weather' => 'array',
        'dont_update_excluded' => 'bool',
        'event_type' => SportEventType::class,
    ];

    public function lastEntryDate(): ?Carbon
    {
        if (!is_null($this->entry_date_3)) {
            return $this->entry_date_3;
        } elseif (!is_null($this->entry_date_2)) {
            return $this->entry_date_2;
        } else {
            return $this->entry_date_1;
        }
    }

    public function sportDiscipline(): HasOne
    {
        return $this->hasOne(SportDiscipline::class, 'id', 'discipline_id');
    }

    public function sportLevel(): HasOne
    {
        return $this->hasOne(SportLevel::class, 'id', 'level_id');
    }

    public function sportClasses(): HasMany
    {
        return $this->hasMany(SportClass::class, 'sport_event_id', 'id');
    }

    public function sportServices(): HasMany
    {
        return $this->hasMany(SportService::class, 'sport_event_id', 'id');
    }

    public function userEntry(): HasMany
    {
        return $this->HasMany(UserEntry::class, 'sport_event_id', 'id');
    }

    public function userEntryActive(): int
    {
        return $this->HasMany(UserEntry::class, 'sport_event_id', 'id')->whereIn('entry_status', ['created'])->count();
    }

    public function sportEventLinks(): HasMany
    {
        return $this->HasMany(SportEventLink::class, 'sport_event_id', 'id');
    }

    public function getSportEventOrisTitleAttribute(): string
    {
        return ($this->alt_name !== null ? $this->alt_name . ' | ' : '') .
            $this->name . ' | ' .
            ($this->oris_id !== null ? '(ORIS ID: ' . $this->oris_id . ')' : '');
    }

    public function getSportEventLastCostCalculateAttribute(): string
    {
        return ($this->alt_name !== null ? $this->alt_name . ' | ' : '') .
            $this->name . ' | ' .
            ($this->oris_id !== null ? '(ORIS ID: ' . $this->oris_id . ')' : '') .
            ($this->last_calculate_cost !== null ? ' | (Náklady naposled : ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->last_calculate_cost)->format('d.h.Y - H:i') . ')' : '')
            ;
    }

}
