<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EntryStatus;
use App\Enums\SportEventType;
use App\Shared\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportEvent
 *
 * @property int $id
 * @property string $name
 * @property string|null $alt_name
 * @property int|null $oris_id
 * @property Carbon|null $date
 * @property Carbon|null $date_end
 * @property string|null $place
 * @property array|null $organization
 * @property array|null $region
 * @property string|null $entry_desc
 * @property string|null $event_info
 * @property string|null $event_warning
 * @property int $sport_id
 * @property int|null $discipline_id
 * @property int|null $level_id
 * @property SportEventType|null $event_type
 * @property bool $use_oris_for_entries
 * @property bool|null $ranking
 * @property float|null $ranking_coefficient
 * @property Carbon|null $entry_date_1
 * @property Carbon|null $entry_date_2
 * @property Carbon|null $entry_date_3
 * @property string|null $increase_entry_fee_2
 * @property string|null $increase_entry_fee_3
 * @property Carbon|null $last_calculate_cost
 * @property string|null $start_time
 * @property string|null $gps_lat
 * @property string|null $gps_lon
 * @property array|null $weather
 * @property int|null $parent_id
 * @property int|null $stages
 * @property int|null $multi_events
 * @property bool $cancelled
 * @property string|null $cancelled_reason
 * @property bool $dont_update_excluded
 * @property Carbon|null $last_update
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SportEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alt_name',
        'oris_id',
        'date',
        'date_end',
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
        'increase_entry_fee_2',
        'increase_entry_fee_3',
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
        'multi_events',
        'dont_update_excluded',
    ];

    protected $casts = [
        'date' => 'date',
        'date_end' => 'date',
        'use_oris_for_entries' => 'bool',
        'ranking' => 'boolean',
        'region' => 'array',
        'organization' => 'array',
        'entry_date_1' => 'datetime:Y-m-d H:i:s',
        'entry_date_2' => 'datetime:Y-m-d H:i:s',
        'entry_date_3' => 'datetime:Y-m-d H:i:s',
        'last_update' => 'datetime:Y-m-d H:i:s',
        'last_calculate_cost' => 'datetime:Y-m-d H:i:s',
        'cancelled' => 'boolean',
        'meta' => 'array',
        'weather' => 'array',
        'dont_update_excluded' => 'boolean',
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

    public function userCredit(): HasMany
    {
        return $this->HasMany(UserCredit::class, 'sport_event_id', 'id');
    }

    public function userEntryActive(): int
    {
        return $this->HasMany(UserEntry::class, 'sport_event_id', 'id')->whereIn(
            'entry_status',
            [EntryStatus::Create, EntryStatus::Edit]
        )->count();
    }

    public function sportEventLinks(): HasMany
    {
        return $this->HasMany(SportEventLink::class, 'sport_event_id', 'id');
    }

    public function sportEventMarkers(): HasMany
    {
        return $this->HasMany(SportEventMarker::class, 'sport_event_id', 'id');
    }

    public function sportEventNews(): HasMany
    {
        return $this->HasMany(SportEventNews::class, 'sport_event_id', 'id');
    }

    public function getSportEventOrisCompactTitleAttribute(): string
    {
        $stringDelimiter = ' | ';
        $date = null;
        if (!is_null($this->date)) {
            $date = $this->date->format(AppHelper::DATE_FORMAT);
        }

        return ($date !== null ? $date : '').
            ((count($this->organization ?? []) > 0) ? $stringDelimiter.Arr::join($this->organization, ', ') : '').
            $stringDelimiter.$this->name.
            ($this->oris_id !== null ? $stringDelimiter.'ORIS ID: '.$this->oris_id : '');
    }

    public function getSportEventOrisTitleAttribute(): string
    {
        return ($this->alt_name !== null ? $this->alt_name.' | ' : '').
            $this->name.' | '.
            ($this->oris_id !== null ? '(ORIS ID: '.$this->oris_id.')' : '');
    }

    public function getSportEventLastCostCalculateAttribute(): string
    {
        return
            $this->date?->format(AppHelper::DATE_FORMAT).' | '.
            Arr::join($this->organization, ', ').' | '.
            ($this->alt_name !== null ? $this->alt_name.' | ' : '').
            $this->name.' | '.
            ($this->oris_id !== null ? '(ORIS ID: '.$this->oris_id.')' : '').
            ($this->last_calculate_cost !== null ? ' | (Náklady naposled : '.$this->last_calculate_cost->format(
                'd.m.Y - H:i'
            ).')' : '');
    }
}
