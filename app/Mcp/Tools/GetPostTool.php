<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí detail jednoho článku včetně obsahu. content_mode: 1=HTML, 2=plaintext, 3=TipTap JSON.')]
#[IsReadOnly]
class GetPostTool extends Tool
{
    public function handle(Request $request): Response
    {
        $postId = $request->get('post_id');

        if ($postId === null) {
            return Response::error('Parametr post_id je povinný.');
        }

        $post = Post::find((int) $postId);

        if ($post === null) {
            return Response::error("Článek s ID {$postId} nebyl nalezen.");
        }

        return Response::json([
            'id'           => $post->id,
            'user_id'      => $post->user_id,
            'title'        => $post->title,
            'editorial'    => $post->editorial,
            'img_url'      => $post->img_url,
            'content'      => $post->content,
            'content_mode' => $post->content_mode instanceof \App\Enums\ContentFormat ? $post->content_mode->value : $post->content_mode,
            'private'      => $post->private instanceof \App\Enums\PostStatus ? $post->private->value : $post->private,
            'created_at'   => $post->created_at,
            'updated_at'   => $post->updated_at,
        ]);
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'post_id' => $schema->integer()->description('ID článku.'),
        ];
    }
}
