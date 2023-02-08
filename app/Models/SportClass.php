<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SportClass
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int|null $oris_id
 * @property int $class_definition_id
 * @property string|null $distance
 * @property string|null $climbing
 * @property string|null $controls
 * @property float|null $fee
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read SportClassDefinition $classDefinition
 */

class SportClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_event_id',
        'oris_id',
        'class_definition_id',
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
