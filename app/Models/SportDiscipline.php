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
 */

class SportDiscipline extends Model
{
    use HasFactory;

    /** @var list<string> */
    public const RELAY_SHORT_NAMES = ['ST', 'SS', 'DR'];

    /** @var list<string> */
    protected $fillable = [
        'short_name',
        'long_name',
    ];

    public function isRelayDiscipline(): bool
    {
        return in_array($this->short_name, self::RELAY_SHORT_NAMES, true);
    }
}
