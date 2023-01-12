<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportEvent
 *
 * @property int $id
 * @property string $name
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read string $sport_event_oris_title
 * @property-read SportDiscipline|null $sportDiscipline
 */

class SportEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
    ];

    protected $casts = [
        'date' => 'date',
        'use_oris_for_entries' => 'bool',
        'ranking' => 'bool',
        'region' => 'array',
        'entry_date_1' => 'datetime:Y-m-d H:i:s',
        'entry_date_2' => 'datetime:Y-m-d H:i:s',
        'entry_date_3' => 'datetime:Y-m-d H:i:s',
    ];


    /**
     * Returns User object for single Post
     *
     * @return HasOne
     */
    public function sportDiscipline(): HasOne
    {
        return $this->hasOne(SportDiscipline::class, 'id', 'discipline_id');
    }

    public function getSportEventOrisTitleAttribute(): string
    {
        return $this->name . ' ' . ($this->oris_id !== null ? '(ORIS ID: ' . $this->oris_id . ')' : '');
    }

}
