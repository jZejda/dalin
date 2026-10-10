<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SportEventLinkType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Override;

/**
 * App\Models\SportEventLink
 *
 * @property int $id
 * @property int $sport_event_id
 * @property int|null $external_key
 * @property bool $internal
 * @property string|null $source_path
 * @property string|null $source_url
 * @property SportEventLinkType $source_type
 * @property string|null $name_cz
 * @property string|null $name_en
 * @property string|null $description_cz
 * @property string|null $description_en
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SportEvent|null $sportEvent
 */
#[Fillable([
    'external_key',
    'sport_event_id',
    'internal',
    'source_path',
    'source_url',
    'source_type',
    'name_cz',
    'name_en',
    'description_cz',
    'description_en',
])]
class SportEventLink extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'internal' => 'boolean',
            'source_type' => SportEventLinkType::class,
        ];
    }

    public function sportEvent(): HasOne
    {
        return $this->hasOne(SportEvent::class, 'id', 'sport_event_id');
    }
}
