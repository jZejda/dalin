<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SportEvent
 *
 * @property int $id
 * @property int|null $oris_id
 * @property int $class_definition_id
 * @property string|null $distance
 */

class SportClass extends Model
{
    use HasFactory;

    protected $fillable = [

    ];

    protected $casts = [

    ];

}
