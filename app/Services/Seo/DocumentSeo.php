<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Models\Document;
use Illuminate\Support\Collection;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Search metadata of the public document list: a CollectionPage of the public documents
 * (members-only ones are never listed) plus a BreadcrumbList.
 */
final class DocumentSeo
{
    public function __construct(private readonly SiteSeo $siteSeo)
    {
    }

    /**
     * @param Collection<int, Document> $publicDocuments
     */
    public function index(Collection $publicDocuments): SEOData
    {
        $title = __('document.public.title');
        $description = __('document.public.description', ['club' => SiteSeo::clubName() ?? '']);
        $url = route('documents.index');

        return new SEOData(
            title: $title,
            description: $description,
            url: $url,
            schema: SchemaCollection::make()
                ->add(fn (): array => $this->collectionPageSchema($publicDocuments, $title, $description, $url))
                ->add(fn (): array => $this->siteSeo->breadcrumbSchema([
                    ['name' => $title, 'url' => $url],
                ])),
        );
    }

    /**
     * @param Collection<int, Document> $documents
     * @return array<string, mixed>
     */
    private function collectionPageSchema(Collection $documents, string $title, string $description, string $url): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => $url,
            'publisher' => $this->siteSeo->publisherReference(),
            'hasPart' => $documents->map(static fn (Document $document): array => array_filter([
                '@type' => 'DigitalDocument',
                'name' => $document->title,
                'url' => $document->publicUrl(),
                'description' => $document->description,
                'encodingFormat' => $document->file()?->mime_type,
                'dateModified' => $document->updated_at?->toIso8601String(),
            ], static fn (mixed $value): bool => $value !== null && $value !== ''))->all(),
        ];
    }
}
