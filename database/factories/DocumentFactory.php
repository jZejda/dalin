<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentCategory;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
#[UseModel(Document::class)]
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $year = $this->faker->numberBetween(2018, 2026);
        $title = 'Výroční zpráva '.$year;

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'description' => null,
            'category' => DocumentCategory::AnnualReport,
            'year' => $year,
            'is_public' => true,
            'sort' => 0,
        ];
    }

    public function membersOnly(): static
    {
        return $this->state(['is_public' => false]);
    }

    public function category(DocumentCategory $category): static
    {
        return $this->state(['category' => $category]);
    }

    /**
     * Attaches a real (tiny) PDF: the media collection checks the detected MIME type,
     * so an empty fake upload would be rejected.
     */
    public function withPdf(?string $fileName = null): static
    {
        return $this->afterCreating(function (Document $document) use ($fileName): void {
            $document->addMediaFromString(self::pdf($document->title))
                ->usingFileName($fileName ?? $document->slug.'.pdf')
                ->usingName($document->title)
                ->toMediaCollection(Document::MEDIA_COLLECTION_FILE);
        });
    }

    /**
     * A valid one-page PDF with the given heading (ASCII only: the built-in Helvetica has no Czech glyphs).
     */
    public static function pdf(string $heading, string $body = ''): string
    {
        $text = static fn (string $value): string => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], Str::ascii($value));
        $stream = "BT /F1 20 Tf 72 760 Td ({$text($heading)}) Tj ET\nBT /F1 11 Tf 72 730 Td ({$text($body)}) Tj ET";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";
    }
}
