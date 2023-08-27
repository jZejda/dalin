<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportClass
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int|null $oris_id
 * @property int $class_definition_id
 * @property string|null $name
 * @property string|null $distance
 * @property string|null $climbing
 * @property string|null $controls
 * @property float|null $fee
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportClassDefinition|null $classDefinition
 * @property-read SportEvent|null $sportEvent
 * @mixin IdeHelperSportClass
 */

class SportClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_event_id',
        'oris_id',
        'class_definition_id',
        'name',
        'distance',
        'climbing',
        'controls',
        'fee',
    ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }

    public function classDefinition(): HasOne
    {
        return $this->hasOne(SportClassDefinition::class, 'id', 'class_definition_id');
    }

}
