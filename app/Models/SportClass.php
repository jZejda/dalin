<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SportClass
 *
 * @property int $id
 * @property int|null $oris_id
 * @property int $class_definition_id
 * @property string|null $distance
 * @property string|null $climbing
 * @property string|null $controls
 * @property float|null $fee
 *
 * @property-read ClassDefinition $classDefinition
 */

class SportClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'oris_id',
        'class_definition_id',
        'distance',
        'climbing',
        'controls',
        'fee',
    ];

    public function classDefinition(): HasOne
    {
        return $this->hasOne(ClassDefinition::class, 'id', 'class_definition_id');
    }

}
