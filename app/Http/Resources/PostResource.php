<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\ContentFormat;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Post $post */
        $post = $this;

        $content = $post->content;

        // Decode TipTap JSON content if content_mode is TipTapJson
        if ($post->content_mode === ContentFormat::TipTapJson) {
            if (is_string($content)) {
                $decoded = json_decode($content, true);
                $content = $decoded !== null ? $decoded : $content;
            }
        }

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'title' => $post->title,
            'editorial' => $post->editorial,
            'img_url' => $post->img_url,
            'content' => $content,
            'content_mode' => $post->content_mode->value,
            'private' => $post->private?->value,
            'created_at' => $post->created_at?->toIso8601String(),
            'updated_at' => $post->updated_at?->toIso8601String(),
        ];
    }
}

