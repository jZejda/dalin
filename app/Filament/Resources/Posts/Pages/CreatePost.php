<?php

declare(strict_types=1);

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    /**
     * The TipTap editor stores its content only after the record exists (its images need the
     * post's media collection), so the NOT NULL column gets a placeholder for the insert.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['content'] ??= '';

        return $data;
    }
}
