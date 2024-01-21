<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportLevel
 *
 * @property int $id
 * @property int|null $oris_id
 * @property string $short_name
 * @property string $long_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SportLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'oris_id',
        'short_name',
        'long_name',
    ];
}
