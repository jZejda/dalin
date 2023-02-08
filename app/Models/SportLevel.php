<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SportLevel
 *
 * @property int $id
 * @property int|null $oris_id
 * @property string $short_name
 * @property string $long_name
 * @property string|null $created_at
 * @property string|null $updated_at
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
