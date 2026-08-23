<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportDiscipline
 *
 * @property int $id
 * @property string $short_name
 * @property string $long_name
 * @property bool $relays
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable(['short_name', 'long_name', 'relays'])]
class SportDiscipline extends Model
{
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'relays' => 'boolean',
        ];
    }

    public function isRelayDiscipline(): bool
    {
        return (bool) $this->relays;
    }
}
