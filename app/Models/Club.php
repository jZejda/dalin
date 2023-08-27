<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Club
 *
 * @property int $id
 * @property string $abbr
 * @property string|null $name
 * @property int $region_id
 * @property string|null $oris_id
 * @property string|null $oris_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportRegion|null $region
 * @mixin IdeHelperClub
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
