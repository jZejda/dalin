<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SportDiscipline
 *
 * @property int $id
 * @property string $short_name
 * @property string $long_name
 * @property string|null $created_at
 * @property string|null $updated_at
 */

class SportDiscipline extends Model
{
    use HasFactory;
}
