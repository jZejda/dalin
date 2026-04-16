<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Page;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí detail jedné stránky včetně obsahu. content_format: 1=HTML, 2=Markdown, 3=TipTap JSON (auto-dekódováno).')]
#[IsReadOnly]
class GetPageTool extends Tool
{
    public function handle(Request $request): Response
    {
        $pageId = $request->get('page_id');

        if ($pageId === null) {
            return Response::error('Parametr page_id je povinný.');
        }

        $page = Page::find((int) $pageId);

        if ($page === null) {
            return Response::error("Stránka s ID {$pageId} nebyla nalezena.");
        }

        return Response::json([
            'id'                  => $page->id,
            'user_id'             => $page->user_id,
            'content_category_id' => $page->content_category_id,
            'title'               => $page->title,
            'slug'                => $page->slug,
            'content'             => $page->content,
            'content_format'      => $page->content_format instanceof \App\Enums\ContentFormat ? $page->content_format->value : $page->content_format,
            'picture_attachment'  => $page->picture_attachment,
            'status'              => $page->status instanceof \App\Enums\PageStatus ? $page->status->value : $page->status,
            'weight'              => $page->weight,
            'page_menu'           => $page->page_menu,
            'meta'                => $page->meta,
            'created_at'          => $page->created_at,
            'updated_at'          => $page->updated_at,
        ]);
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'page_id' => $schema->integer()->description('ID stránky.'),
        ];
    }
}
