<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Override;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A club document (annual report, statutes, minutes) published on /dokumenty.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property DocumentCategory $category
 * @property int|null $year
 * @property bool $is_public
 * @property int $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
#[Fillable([
    'user_id',
    'title',
    'slug',
    'description',
    'category',
    'year',
    'is_public',
    'sort',
])]
class Document extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;
    use InteractsWithMedia;

    public const string MEDIA_COLLECTION_FILE = 'document_file';

    public const string DISK = 'documents';

    public const int MAX_FILE_SIZE_KB = 20480;

    /** PDF, Word, Excel, OpenDocument and images (e.g. a scanned page of the minutes). */
    public const array ACCEPTED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'category' => DocumentCategory::class,
            'year' => 'integer',
            'is_public' => 'boolean',
            'sort' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Documents the visitor may see: public ones for everybody, all of them for a logged-in member.
     *
     * @param Builder<Document> $query
     */
    #[Scope]
    protected function visibleTo(Builder $query, ?User $user): void
    {
        if ($user === null) {
            $query->where('is_public', true);
        }
    }

    /**
     * Newest year first, then the order set in the admin.
     *
     * @param Builder<Document> $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderByDesc('year')->orderBy('sort')->orderBy('title');
    }

    public function file(): ?Media
    {
        return $this->getFirstMedia(self::MEDIA_COLLECTION_FILE);
    }

    /**
     * Permanent URL that always serves the current file.
     */
    public function publicUrl(): string
    {
        return route('documents.show', ['document' => $this->slug]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_FILE)
            ->useDisk(self::DISK)
            ->acceptsMimeTypes(self::ACCEPTED_MIME_TYPES)
            ->singleFile();
    }
}
