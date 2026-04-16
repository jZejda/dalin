<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí seznam článků/novinek s filtrováním podle data vytvoření.')]
#[IsReadOnly]
class PostsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $query = Post::query();

        $from = $request->get('from');
        $to   = $request->get('to');

        if ($from !== null) {
            $query->where('created_at', '>=', Carbon::parse((string) $from)->startOfDay());
        }

        if ($to !== null) {
            $query->where('created_at', '<=', Carbon::parse((string) $to)->endOfDay());
        }

        $perPage = min((int) ($request->get('per_page', 20) ?: 20), 100);

        $posts = $query
            ->orderByDesc('created_at')
            ->select(['id', 'user_id', 'title', 'editorial', 'img_url', 'content_mode', 'private', 'created_at', 'updated_at'])
            ->simplePaginate($perPage);

        return Response::json($posts->toArray());
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'from'     => $schema->string()->description('Datum od (Y-m-d).'),
            'to'       => $schema->string()->description('Datum do (Y-m-d).'),
            'per_page' => $schema->integer()->description('Počet záznamů na stránku (max 100, výchozí 20).'),
        ];
    }
}
