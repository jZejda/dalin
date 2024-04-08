<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\SportEventNews
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int $text
 * @property Carbon $date
 * @property-read SportEvent|null $sportEvent
 */
class SportEventNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }
}
