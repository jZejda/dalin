<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
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
 * @property int $content_mode
 * @property bool|null $private
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
class Post extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;
    use InteractsWithRichContent;

    protected $casts = [
        'private' => PostStatus::class,
        'content_mode' => ContentFormat::class,
    ];

    /* @var array<string> */
    protected $fillable = [
        'title',
        'content',
        'private',
        'user_id',
        'content_mode',
        'img_url',
        'editorial',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

//    public function setUpRichContent(): void
//    {
//        $this->registerRichContent('content')
//            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make())
//            ->mediaName(fn (TemporaryUploadedFile $file): string => Str::random() . '_' . $file->getClientOriginalName())
//            ->collection('content-file-attachments');
//    }

//    public function registerMediaConversions(?Media $media = null): void
//    {
//        $this
//            ->addMediaConversion('preview')
//            ->fit(Fit::Contain, 300, 300)
//            ->nonQueued();
//    }
}
