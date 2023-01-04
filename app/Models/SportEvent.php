<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SportEvent extends Model
{
    use HasFactory;

    /**
     * App\Models\SportEvent
     *
     * @property int $id
     * @property string $name
     * @property int $oris_id
     * @property string $date
     * @property ?string $place
     * @property ?string $region
     * @property ?int $sport_id
     * @property ?int $discipline_id
     * @property ?int $use_oris_for_entries
     * @property ?int $ranking
     * @property ?float $ranking_coefficient
     * @property string $entry_date_1
     * @property ?string $entry_date_2
     * @property ?string $entry_date_3
     * @property string $updated_at
     *
     * @property string $user_identification
     */

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
        return "{$this->name}  (Oris ID: {$this->oris_id})";
    }

}
