<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SportEventLinkSource;
use App\Enums\SportEventLinkType;
use App\Observers\SportEventLinkObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
#[ObservedBy(SportEventLinkObserver::class)]
class SportEventLink extends Model
{
    use HasFactory;

    /** Public disk for files uploaded to DaLin (source_path is relative to it). */
    public const string FILE_DISK = 'events';

    /** Uploaded files go to <FILE_DIRECTORY>/<sport_event_id>/ on FILE_DISK. */
    public const string FILE_DIRECTORY = 'links';

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

    /**
     * Human-readable name: the ORIS/manual name, else its description (ORIS "other" links carry
     * the meaningful text there), else the link type. English falls back to the Czech texts,
     * which say more than a generic type label.
     */
    public function label(): string
    {
        $candidates = app()->getLocale() === 'en'
            ? [$this->name_en, $this->description_en, $this->name_cz, $this->description_cz]
            : [$this->name_cz, $this->description_cz];

        foreach ($candidates as $candidate) {
            if (filled($candidate)) {
                return trim($candidate);
            }
        }

        return __('sport-event.type_enum_links.'.$this->source_type->value);
    }

    /**
     * Target of the link: the public URL of a file uploaded to DaLin, else the external URL.
     */
    public function url(): ?string
    {
        if ($this->isUploadedFile()) {
            return Storage::disk(self::FILE_DISK)->url((string) $this->source_path);
        }

        return $this->source_url;
    }

    public function isUploadedFile(): bool
    {
        return filled($this->source_path);
    }

    /**
     * Where the link leads; a file uploaded to DaLin (source_path) always resolves to DaLin.
     */
    public function source(): SportEventLinkSource
    {
        if ($this->isUploadedFile()) {
            return SportEventLinkSource::Dalin;
        }

        return SportEventLinkSource::fromUrl($this->source_url);
    }
}
