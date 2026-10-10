<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\SportEventLink;
use Illuminate\Support\Facades\Storage;

/**
 * Keeps files uploaded to DaLin in sync with their links: a replaced or
 * deleted file must not stay publicly reachable on the events disk.
 */
class SportEventLinkObserver
{
    public function updated(SportEventLink $link): void
    {
        if ($link->wasChanged('source_path')) {
            $this->deleteFile($link->getOriginal('source_path'));
        }
    }

    public function deleted(SportEventLink $link): void
    {
        $this->deleteFile($link->source_path);
    }

    private function deleteFile(mixed $path): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk(SportEventLink::FILE_DISK)->delete($path);
        }
    }
}
