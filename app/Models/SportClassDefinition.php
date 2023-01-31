<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\SportClassDefinition
 *
 * @property int $id
 * @property int|null $oris_id
 * @property int $sport_id
 * @property int $age_from
 * @property int $age_to
 * @property string|null $gender
 * @property string $name
 *
 * @property-read SportList $sport
 */
class SportClassDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'oris_id',
        'sport_id',
        'age_from',
        'age_to',
        'gender',
        'name'
    ];

    public function sport(): HasOne
    {
        return $this->hasOne(SportList::class, 'id', 'sport_id');
    }

    public function getClassDefinitionFullLabelAttribute(): string
    {
        $genderName = 'Vše';
        if ($this->gender === 'M') {
            $genderName = 'Muž';
        } elseif ($this->gender === 'F') {
            $genderName = 'Žena';
        }

        return "{$this->name}  ({$this->age_from}-{$this->age_to}) ($genderName)";
    }

}
