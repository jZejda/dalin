<?php

declare(strict_types=1);

use App\Enums\DocumentCategory;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use App\Models\User;
use Database\Factories\DocumentFactory;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Storage::fake(Document::DISK);
    Cache::flush();
    config(['site-config.club.full_name' => 'OK Testov', 'site-config.club.abbr' => 'TST']);
});

function fakePdfUpload(string $name, string $heading = 'Dokument'): File
{
    return UploadedFile::fake()->createWithContent($name, DocumentFactory::pdf($heading));
}

/**
 * @return array<string, array<string, mixed>>
 */
function documentJsonLd(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    return collect($matches[1])
        ->map(static fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR))
        ->keyBy('@type')
        ->all();
}

describe('admin', function (): void {
    it('creates a document with an uploaded file on the private documents disk', function (): void {
        $admin = actingAsSuperAdmin();

        livewire(CreateDocument::class)
            ->fillForm([
                'title' => 'Výroční zpráva 2025',
                'category' => DocumentCategory::AnnualReport,
                'year' => 2025,
                'is_public' => true,
                'sort' => 0,
                'file' => fakePdfUpload('vyrocni-zprava-2025.pdf'),
            ])
            ->assertSchemaStateSet(['slug' => 'vyrocni-zprava-2025'])
            ->call('create')
            ->assertHasNoFormErrors();

        $document = Document::query()->where('slug', 'vyrocni-zprava-2025')->firstOrFail();
        $file = $document->file();

        expect($document->user_id)->toBe($admin->id)
            ->and($file)->not->toBeNull()
            ->and($file?->disk)->toBe(Document::DISK)
            ->and($file?->file_name)->toBe('vyrocni-zprava-2025.pdf');

        Storage::disk(Document::DISK)->assertExists($file?->getPathRelativeToRoot() ?? '');
    });

    it('requires a file and a unique slug', function (): void {
        actingAsSuperAdmin();
        Document::factory()->create(['slug' => 'stanovy']);

        livewire(CreateDocument::class)
            ->fillForm(['title' => 'Stanovy', 'slug' => 'stanovy', 'category' => DocumentCategory::Statutes, 'sort' => 0])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique', 'file' => 'required']);
    });

    it('lists documents in the admin table', function (): void {
        actingAsSuperAdmin();
        $documents = Document::factory()->count(2)->withPdf()->create();

        livewire(ListDocuments::class)
            ->assertOk()
            ->assertCanSeeTableRecords($documents);
    });

    it('keeps the permanent URL when the file is replaced', function (): void {
        actingAsSuperAdmin();
        $document = Document::factory()->withPdf()->create(['slug' => 'smernice']);
        $url = $document->publicUrl();

        livewire(EditDocument::class, ['record' => $document->getRouteKey()])
            ->fillForm(['file' => fakePdfUpload('smernice-2026.pdf', 'Nova verze')])
            ->call('save')
            ->assertHasNoFormErrors();

        $document->refresh();

        expect($document->getMedia(Document::MEDIA_COLLECTION_FILE))->toHaveCount(1)
            ->and($document->file()?->file_name)->toBe('smernice-2026.pdf')
            ->and($document->publicUrl())->toBe($url);

        $this->get($url)->assertOk()->assertStreamedContent(DocumentFactory::pdf('Nova verze'));
    });
});

describe('public list', function (): void {
    it('groups public documents by category and hides members-only ones from guests', function (): void {
        Document::factory()->withPdf()->create(['title' => 'Výroční zpráva 2024', 'year' => 2024]);
        Document::factory()->withPdf()->create(['title' => 'Výroční zpráva 2025', 'year' => 2025]);
        Document::factory()->category(DocumentCategory::Statutes)->withPdf()->create(['title' => 'Stanovy spolku', 'year' => null]);
        Document::factory()->category(DocumentCategory::Minutes)->membersOnly()->withPdf()->create(['title' => 'Zápis z valné hromady']);
        Document::factory()->create(['title' => 'Dokument bez souboru']);

        $this->get(route('documents.index'))
            ->assertOk()
            ->assertSee('<h1', escape: false)
            ->assertSeeInOrder(['Výroční zprávy', 'Výroční zpráva 2025', 'Výroční zpráva 2024', 'Stanovy a předpisy', 'Stanovy spolku'])
            ->assertDontSee('Zápis z valné hromady')
            ->assertDontSee('Zápisy ze schůzí')
            ->assertDontSee('Dokument bez souboru')
            ->assertSee(__('document.public.members_hint'));
    });

    it('shows members-only documents with a badge to logged-in members', function (): void {
        $this->actingAs(User::factory()->create());
        Document::factory()->category(DocumentCategory::Minutes)->membersOnly()->withPdf()->create(['title' => 'Zápis z valné hromady']);

        $this->get(route('documents.index'))
            ->assertOk()
            ->assertSee('Zápis z valné hromady')
            ->assertSee(__('document.public.members_only'))
            ->assertDontSee(__('document.public.members_hint'));
    });

    it('renders title, description and CollectionPage + BreadcrumbList JSON-LD without members-only documents', function (): void {
        $public = Document::factory()->withPdf()->create(['title' => 'Výroční zpráva 2025']);
        Document::factory()->membersOnly()->withPdf()->create(['title' => 'Interní zápis']);

        $html = (string) $this->actingAs(User::factory()->create())->get(route('documents.index'))->assertOk()->getContent();
        $jsonLd = documentJsonLd($html);

        expect($html)
            ->toContain('<title>Dokumenty | TST</title>')
            ->toContain('<meta name="description" content="Výroční zprávy, stanovy a zápisy ze schůzí klubu OK Testov ke stažení.">')
            ->toContain('<link rel="canonical" href="'.route('documents.index').'"')
            ->and($jsonLd['CollectionPage']['hasPart'])->toHaveCount(1)
            ->and($jsonLd['CollectionPage']['hasPart'][0])->toMatchArray([
                '@type' => 'DigitalDocument',
                'name' => 'Výroční zpráva 2025',
                'url' => $public->publicUrl(),
                'encodingFormat' => 'application/pdf',
            ])
            ->and($jsonLd['BreadcrumbList']['itemListElement'][1]['item'])->toBe(route('documents.index'));
    });
});

describe('file download', function (): void {
    it('streams a public document inline', function (): void {
        $document = Document::factory()->withPdf('vyrocni-zprava.pdf')->create();

        $this->get($document->publicUrl())
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename="vyrocni-zprava.pdf"')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeaderMissing('X-Robots-Tag');
    });

    it('sends guests to the login for members-only documents', function (): void {
        $document = Document::factory()->membersOnly()->withPdf()->create();

        $this->get($document->publicUrl())->assertRedirect(route('filament.admin.auth.login'));

        expect(session('url.intended'))->toBe($document->publicUrl());
    });

    it('serves members-only documents to members, never cached or indexed', function (): void {
        $document = Document::factory()->membersOnly()->withPdf()->create();

        $this->actingAs(User::factory()->create())
            ->get($document->publicUrl())
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    });

    it('returns 404 for an unknown slug or a document without a file', function (): void {
        $withoutFile = Document::factory()->create();

        $this->get(route('documents.show', ['document' => 'neexistuje']))->assertNotFound();
        $this->get($withoutFile->publicUrl())->assertNotFound();
    });
});

it('lists the document page and public files in the sitemap, never members-only ones', function (): void {
    $public = Document::factory()->withPdf()->create();
    $membersOnly = Document::factory()->membersOnly()->withPdf()->create();

    $xml = (string) $this->get('/sitemap.xml')->assertOk()->getContent();

    expect($xml)
        ->toContain('<loc>'.route('documents.index').'</loc>')
        ->toContain('<loc>'.$public->publicUrl().'</loc>')
        ->not->toContain('<loc>'.$membersOnly->publicUrl().'</loc>');
});
