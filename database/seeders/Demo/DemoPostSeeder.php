<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoPostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $admin = User::where('email', 'admin@demo.cz')->first();

        if ($admin === null) {
            return;
        }

        $titles = [
            'Výsledky z krajského přeboru',
            'Pozvánka na víkendový závod',
            'Novinky ze sezony 2026',
            'Změna termínu závodů v dubnu',
            'Soustředění mládeže – přihlášky',
            'Výsledky mistrovství ČR',
            'Nový tréninkový plán na jaro',
            'Informace o členských příspěvcích 2026',
            'Poháry a ocenění z loňské sezony',
            'Technické informace – nový mapový software',
            'Výbor klubu – zápis ze schůze',
            'Víkend v Jeseníkách – přihlašování',
            'Orientační závod pro školy',
            'Podmínky registrace pro nové členy',
            'Konec sezony – bilancování roku',
        ];

        foreach ($titles as $index => $title) {
            $isPrivate = $faker->boolean(30);
            $createdAt = Carbon::now()->subDays($faker->numberBetween(1, 300));

            /** @var list<string> $paragraphs */
            $paragraphs = $faker->paragraphs($faker->numberBetween(2, 5));

            Post::create([
                'user_id'      => $admin->id,
                'title'        => $title,
                'editorial'    => $faker->optional(0.7)->sentence(),
                'content'      => '<p>' . implode('</p><p>', $paragraphs) . '</p>',
                'content_mode' => ContentFormat::Html->value,
                'private'      => $isPrivate ? PostStatus::Private->value : PostStatus::Public->value,
                'created_at'   => $createdAt->toDateTimeString(),
                'updated_at'   => $createdAt->toDateTimeString(),
            ]);
        }
    }
}
