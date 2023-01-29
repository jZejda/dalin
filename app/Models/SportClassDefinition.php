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
 * @property int|null $oris_id
 * @property int $sport_id
 * @property int $age_from
 * @property int $age_to
 * @property string|null $gender
 * @property string $name
 */

class SportClassDefinition extends Model
{
    use HasFactory;

    protected $fillable = [

    ];

    protected $casts = [

    ];

}
