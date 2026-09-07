<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportList
 *
 * @property int $id
 * @property string $short_name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['short_name', 'color'])]
class SportList extends Model
{
    use HasFactory;
}
