<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SportRegion
 *
 * @property int $id
 * @property string $short_name
 * @property string $long_name
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class SportRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_name',
        'long_name',
    ];
}
