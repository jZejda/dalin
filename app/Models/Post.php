<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $editorial
 * @property string|null $img_url
 * @property string $content
 * @property ContentFormat $content_mode
 * @property PostStatus|null $private
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
#[Fillable([
    'title',
    'content',
    'private',
    'user_id',
    'content_mode',
    'img_url',
    'editorial',
])]
class Post extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;
    use InteractsWithRichContent;

    public const string MEDIA_COLLECTION_COVER = 'post_cover';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'private' => PostStatus::class,
            'content_mode' => ContentFormat::class,
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Cover image URL for the given media conversion ('card' 4:3 crop for news cards, 'detail' uncropped for the detail page).
     * An uploaded cover wins; otherwise an absolute img_url (e.g. set via API) is used as is.
     * Legacy relative img_url paths ("media/2022/06/thubnails/a.png") point to files lost in the
     * old-site migration, so they are ignored and the caller renders its placeholder instead.
     */
    public function coverUrl(string $conversion = 'card'): ?string
    {
        $media = $this->getFirstMedia(self::MEDIA_COLLECTION_COVER);

        if ($media !== null) {
            return $media->getAvailableUrl([$conversion]);
        }

        $path = trim((string) $this->img_url);

        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_COVER)
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Non-queued: shared hosting sites don't run a queue worker
        $this->addMediaConversion('card')
            ->performOnCollections(self::MEDIA_COLLECTION_COVER)
            ->nonQueued()
            ->fit(Fit::Crop, 800, 600)
            ->format('jpg');

        // Detail page shows the whole photo, so only downscale and keep the original aspect ratio
        $this->addMediaConversion('detail')
            ->performOnCollections(self::MEDIA_COLLECTION_COVER)
            ->nonQueued()
            ->fit(Fit::Max, 1600, 1600)
            ->format('jpg');
    }

    //    public function setUpRichContent(): void
    //    {
    //        $this->registerRichContent('content')
    //            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make())
    //            ->mediaName(fn (TemporaryUploadedFile $file): string => Str::random() . '_' . $file->getClientOriginalName())
    //            ->collection('content-file-attachments');
    //    }
}
