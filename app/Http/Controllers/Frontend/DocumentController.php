<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Enums\DocumentCategory;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Services\Seo\DocumentSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request, DocumentSeo $documentSeo): View
    {
        /** @var User|null $user */
        $user = $request->user();

        $documents = Document::query()
            ->with('media')
            ->visibleTo($user)
            ->ordered()
            ->get()
            ->filter(static fn (Document $document): bool => $document->file() !== null);

        // Sections in the order of the enum cases, empty categories left out
        $sections = collect(DocumentCategory::cases())
            ->map(static fn (DocumentCategory $category): array => [
                'category' => $category,
                'documents' => $documents->filter(static fn (Document $document): bool => $document->category === $category)->values(),
            ])
            ->filter(static fn (array $section): bool => $section['documents']->isNotEmpty())
            ->values();

        return view('pages.frontend.documents', [
            'sections' => $sections,
            'seo' => $documentSeo->index($documents->where('is_public', true)->values()),
            'sponsorSectionId' => 0,
        ]);
    }

    /**
     * Permanent URL of a document: streams whatever file is attached now, so replacing the
     * file in the admin never breaks links shared in mails or on other sites.
     */
    public function show(Request $request, string $document): StreamedResponse|RedirectResponse
    {
        $model = Document::query()->with('media')->where('slug', $document)->first();
        $file = $model?->file();

        if ($model === null || $file === null) {
            abort(404);
        }

        if (! $model->is_public && $request->user() === null) {
            // Filament's login sends the member back here afterwards
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        $response = $file->toInlineResponse($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        if (! $model->is_public) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            $response->headers->set('Cache-Control', 'private, no-store');
        }

        return $response;
    }
}
