<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportDiscipline
 *
 * @property int $id
 * @property string $short_name
 * @property string $long_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin IdeHelperSportDiscipline
 */

class SportDiscipline extends Model
{
    use HasFactory;
}
