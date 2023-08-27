<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EntryStatus;
use Arr;
use App\Enums\SportEventType;
use App\Shared\Helpers\AppHelper;
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
 * @property Carbon|null $date
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
 * @property Carbon|null $last_update
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $sport_event_last_cost_calculate
 * @property-read string $sport_event_oris_title
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SportClass> $sportClasses
 * @property-read int|null $sport_classes_count
 * @property-read \App\Models\SportDiscipline|null $sportDiscipline
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SportEventLink> $sportEventLinks
 * @property-read int|null $sport_event_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SportEventMarker> $sportEventMarkers
 * @property-read int|null $sport_event_markers_count
 * @property-read \App\Models\SportLevel|null $sportLevel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SportService> $sportServices
 * @property-read int|null $sport_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserEntry> $userEntry
 * @property-read int|null $user_entry_count
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereAltName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereCancelledReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereDontUpdateExcluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEntryDate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEntryDate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEntryDate3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEntryDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEventInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereEventWarning($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereGpsLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereGpsLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereLastCalculateCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereLastUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereMultiEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereOrisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereRanking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereRankingCoefficient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereStages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereUseOrisForEntries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportEvent whereWeather($value)
 * @mixin \Eloquent
 * @mixin IdeHelperSportEvent
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
        'multi_events',
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
        return $this->HasMany(UserEntry::class, 'sport_event_id', 'id')->whereIn('entry_status', [EntryStatus::Create, EntryStatus::Edit])->count();
    }

    public function sportEventLinks(): HasMany
    {
        return $this->HasMany(SportEventLink::class, 'sport_event_id', 'id');
    }

    public function sportEventMarkers(): HasMany
    {
        return $this->HasMany(SportEventMarker::class, 'sport_event_id', 'id');
    }

    public function getSportEventOrisTitleAttribute(): string
    {
        return ($this->alt_name !== null ? $this->alt_name . ' | ' : '') .
            $this->name . ' | ' .
            ($this->oris_id !== null ? '(ORIS ID: ' . $this->oris_id . ')' : '');
    }

    public function getSportEventLastCostCalculateAttribute(): string
    {
        return
            (Carbon::createFromFormat(AppHelper::MYSQL_DATE_TIME, $this->date)->format(AppHelper::DATE_FORMAT)) . ' | '  .
            Arr::join($this->organization, ', ') . ' | ' .
            ($this->alt_name !== null ? $this->alt_name . ' | ' : '') .
            $this->name . ' | ' .
            ($this->oris_id !== null ? '(ORIS ID: ' . $this->oris_id . ')' : '') .
            ($this->last_calculate_cost !== null ? ' | (Náklady naposled : ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->last_calculate_cost)->format('d.h.Y - H:i') . ')' : '')
        ;
    }

}
