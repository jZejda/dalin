<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@demo.cz')->first();

        $documents = [
            ['Výroční zpráva 2025', DocumentCategory::AnnualReport, 2025, true, 'Sportovní výsledky, hospodaření a činnost oddílu v sezoně 2025.'],
            ['Výroční zpráva 2024', DocumentCategory::AnnualReport, 2024, true, 'Mistrovství oblasti, nová mapa Babí lom a 20 nových členů.'],
            ['Výroční zpráva 2023', DocumentCategory::AnnualReport, 2023, true, null],
            ['Stanovy spolku', DocumentCategory::Statutes, null, true, 'Úplné znění stanov schválené valnou hromadou 15. 2. 2024.'],
            ['Směrnice o cestovních náhradách', DocumentCategory::Statutes, 2025, true, 'Příspěvky na dopravu a startovné na závody žebříčku B.'],
            ['Zápis z valné hromady 2026', DocumentCategory::Minutes, 2026, false, 'Volba výboru, rozpočet na rok 2026 a výše členských příspěvků.'],
            ['Zápis ze schůze výboru – září 2026', DocumentCategory::Minutes, 2026, false, null],
            ['Přihláška za člena klubu', DocumentCategory::Other, null, true, 'Vyplněnou přihlášku odevzdejte trenérovi nebo pošlete na e-mail výboru.'],
        ];

        foreach ($documents as $sort => [$title, $category, $year, $isPublic, $description]) {
            $document = Document::query()->create([
                'user_id' => $admin?->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'category' => $category,
                'year' => $year,
                'is_public' => $isPublic,
                'sort' => $sort,
            ]);

            $document->addMediaFromString(DocumentFactory::pdf($title, (string) config('site-config.club.full_name')))
                ->usingFileName(Str::slug($title).'.pdf')
                ->usingName($title)
                ->toMediaCollection(Document::MEDIA_COLLECTION_FILE);
        }
    }
}
