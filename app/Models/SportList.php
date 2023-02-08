<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SportList
 *
 * @property int $id
 * @property string $short_name
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class SportList extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_name',
    ];

}
