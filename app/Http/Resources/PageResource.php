<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\ContentFormat;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    protected bool $includeContent;

    public function __construct(Page $resource, bool $includeContent = false)
    {
        parent::__construct($resource);
        $this->includeContent = $includeContent;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this;

        $data = [
            'id' => $page->id,
            'user_id' => $page->user_id,
            'content_category_id' => $page->content_category_id,
            'title' => $page->title,
            'slug' => $page->slug,
            'content_format' => $page->content_format,
            'picture_attachment' => $page->picture_attachment,
            'status' => $page->status,
            'weight' => $page->weight,
            'page_menu' => $page->page_menu,
            'meta' => $page->meta,
            'created_at' => $page->created_at?->toIso8601String(),
            'updated_at' => $page->updated_at?->toIso8601String(),
        ];

        if ($this->includeContent) {
            $content = $page->content;

            // Decode TipTap JSON content if content_format is TipTapJson
            if ($page->content_format === ContentFormat::TipTapJson) {
                if (is_string($content)) {
                    $decoded = json_decode($content, true);
                    $content = $decoded !== null ? $decoded : $content;
                }
            }

            $data['content'] = $content;
        }

        return $data;
    }
}

