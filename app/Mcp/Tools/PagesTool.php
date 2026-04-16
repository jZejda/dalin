<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí seznam stránek s filtrováním podle data, stavu a kategorie obsahu.')]
#[IsReadOnly]
class PagesTool extends Tool
{
    public function handle(Request $request): Response
    {
        $query = Page::query();

        $from   = $request->get('from');
        $to     = $request->get('to');
        $status = $request->get('status');
        $contentCategoryId = $request->get('content_category_id');

        if ($from !== null) {
            $query->where('created_at', '>=', Carbon::parse((string) $from)->startOfDay());
        }

        if ($to !== null) {
            $query->where('created_at', '<=', Carbon::parse((string) $to)->endOfDay());
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($contentCategoryId !== null) {
            $query->where('content_category_id', (int) $contentCategoryId);
        }

        $perPage = min((int) ($request->get('per_page', 20) ?: 20), 100);

        $pages = $query
            ->orderByDesc('created_at')
            ->select(['id', 'user_id', 'content_category_id', 'title', 'slug', 'content_format', 'picture_attachment', 'status', 'weight', 'page_menu', 'meta', 'created_at', 'updated_at'])
            ->simplePaginate($perPage);

        return Response::json($pages->toArray());
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'from'                => $schema->string()->description('Datum od (Y-m-d).'),
            'to'                  => $schema->string()->description('Datum do (Y-m-d).'),
            'status'              => $schema->string()->description('Stav stránky: open, close, draft, archive.'),
            'content_category_id' => $schema->integer()->description('ID kategorie obsahu.'),
            'per_page'            => $schema->integer()->description('Počet záznamů na stránku (max 100, výchozí 20).'),
        ];
    }
}
