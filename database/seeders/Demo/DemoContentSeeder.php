<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');
        $admin = User::where('email', 'admin@demo.cz')->first();

        if ($admin === null) {
            return;
        }

        $categories = [
            'O klubu'   => 'Základní informace o klubu a jeho fungování',
            'Pro členy' => 'Praktické informace pro členy klubu',
            'Tréninky'  => 'Tréninkové materiály a termíny',
        ];

        $categoryModels = [];

        foreach ($categories as $title => $description) {
            $categoryModels[$title] = ContentCategory::factory()->create([
                'title'       => $title,
                'description' => $description,
                'slug'        => Str::slug($title),
            ]);
        }

        $pages = [
            ['title' => 'O našem klubu',                'category' => 'O klubu',   'menu' => true],
            ['title' => 'Kontakty a výbor klubu',       'category' => 'O klubu',   'menu' => true],
            ['title' => 'Členské příspěvky',            'category' => 'Pro členy', 'menu' => true],
            ['title' => 'Jak se přihlásit na závod',    'category' => 'Pro členy', 'menu' => false],
            ['title' => 'Tréninkové mapy ke stažení',   'category' => 'Tréninky',  'menu' => false],
            ['title' => 'Rozpis tréninků – jaro 2026',  'category' => 'Tréninky',  'menu' => true],
        ];

        foreach ($pages as $page) {
            /** @var list<string> $paragraphs */
            $paragraphs = $faker->paragraphs($faker->numberBetween(3, 6));

            $factory = Page::factory();

            if ($page['menu']) {
                $factory = $factory->inMenu();
            }

            $factory->create([
                'user_id'             => $admin->id,
                'content_category_id' => $categoryModels[$page['category']]->id,
                'title'               => $page['title'],
                'slug'                => Str::slug($page['title']),
                'content'             => '<p>' . implode('</p><p>', $paragraphs) . '</p>',
            ]);
        }
    }
}
