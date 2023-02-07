<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 *
 * App\Models\Club
 *
 * @property string $abbr
 * @property ?string $name
 * @property ?string $region_id
 * @property ?int $oris_id
 * @property ?int $oris_number
 *
 * @property-read SportRegion $region
 *
 */
class Club extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'abbr',
        'name',
        'region_id',
        'oris_id',
        'oris_number',
    ];

    public function region(): HasOne
    {
        return $this->hasOne(SportRegion::class, 'id', 'region_id');
    }

}
