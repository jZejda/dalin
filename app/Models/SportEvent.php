<?php

declare(strict_types=1);

namespace App\Models;

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
 * @property ?string $alt_name
 * @property int|null $oris_id
 * @property Carbon $date
 * @property string|null $place
 * @property array|null $region
 * @property int $sport_id
 * @property int $discipline_id
 * @property bool $use_oris_for_entries
 * @property bool|null $ranking
 * @property float|null $ranking_coefficient
 * @property Carbon $entry_date_1
 * @property Carbon|null $entry_date_2
 * @property Carbon|null $entry_date_3
 * @property bool $dont_update_excluded
 * @property Carbon|null $last_update
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read string $sport_event_oris_title
 * @property-read SportDiscipline|null $sportDiscipline
 * @property-read SportService|null $sportServices
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
        'sport_id',
        'discipline_id',
        'use_oris_for_entries',
        'ranking',
        'ranking_coefficient',
        'entry_date_1',
        'entry_date_2',
        'entry_date_3',
        'last_update',
        'dont_update_excluded',
    ];

    protected $casts = [
        'date' => 'date',
        'use_oris_for_entries' => 'bool',
        'ranking' => 'bool',
        'region' => 'array',
        'entry_date_1' => 'datetime:Y-m-d H:i:s',
        'entry_date_2' => 'datetime:Y-m-d H:i:s',
        'entry_date_3' => 'datetime:Y-m-d H:i:s',
        'last_update' => 'datetime:Y-m-d H:i:s',
        'dont_update_excluded' => 'bool',
    ];


    public function sportDiscipline(): HasOne
    {
        return $this->hasOne(SportDiscipline::class, 'id', 'discipline_id');
    }

    public function sportClasses(): HasMany
    {
        return $this->hasMany(SportClass::class, 'sport_event_id', 'id');
    }

    public function sportServices(): HasMany
    {
        return $this->hasMany(SportService::class, 'sport_event_id', 'id');
    }

    public function getSportEventOrisTitleAttribute(): string
    {
        return $this->name . ' ' . ($this->oris_id !== null ? '(ORIS ID: ' . $this->oris_id . ')' : '');
    }

}
