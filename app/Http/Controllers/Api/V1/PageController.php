<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group("V1", "APIs V1")]
#[Subgroup("PAGE", "Page")]
class PageController extends Controller
{
    /**
     * Seznam stránek
     *
     * Vrátí stránkovaný seznam obsahových stránek klubu.
     */
    #[QueryParam('from', 'string', description:'Date from in Y-M-D format', required: false, example: '2024-12-31')]
    #[QueryParam('to', 'string', description: 'Date to in Y-M-D format', required: false, example: '2024-12-31')]
    #[QueryParam('page', 'integer', description: 'Page number for pagination', required: false, example: 1)]
    #[QueryParam('per_page', 'integer', description: 'Number of items per page. Defaults to 20.', required: false, example: 20)]
    #[QueryParam('status', 'string', description: 'Filter by page status (open, close, draft, archive)', required: false, example: 'open')]
    #[QueryParam('content_category_id', 'integer', description: 'Filter by content category ID', required: false, example: 1)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/page.list.json', 200, description: 'Example Page List')]
    public function list(Request $request): JsonResource
    {
        $query = Page::query();

        $from = $request->query('from');
        $to = $request->query('to');

        if ($from !== null) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }

        if ($to !== null) {
            $toDate = Carbon::parse($to)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        }

        $status = $request->query('status');
        if ($status !== null) {
            $query->where('status', $status);
        }

        $contentCategoryId = $request->query('content_category_id');
        if ($contentCategoryId !== null) {
            $query->where('content_category_id', $contentCategoryId);
        }

        $pages = $query->orderByDesc('created_at')
            ->select([
                'id', 'user_id', 'content_category_id', 'title', 'slug', 'content_format',
                'picture_attachment', 'status', 'weight', 'page_menu', 'meta', 'created_at', 'updated_at'
            ])
            ->simplePaginate($request->query('per_page', 20));

        $pages->getCollection()->transform(function ($page) {
            return new PageResource($page, false);
        });

        return JsonResource::collection($pages);
    }

    /**
     * Detail stránky
     *
     * Vrátí detail konkrétní obsahové stránky podle ID.
     */
    //    #[UrlParam('page', 'integer', 'The ID of the Page.', required: true, example: 1)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/page.detail.html.json', 200, description: 'Example with HTML content (content_format = 1)')]
    #[ResponseFromFile('app/Docs/Api/V1/Response/page.detail.markdown.json', 200, description: 'Example with Markdown content (content_format = 2)')]
    #[ResponseFromFile('app/Docs/Api/V1/Response/page.detail.tiptap.json', 200, description: 'Example with TipTap JSON content (content_format = 3)')]
    public function detail(Page $page): PageResource
    {
        return new PageResource($page, true);
    }
}
