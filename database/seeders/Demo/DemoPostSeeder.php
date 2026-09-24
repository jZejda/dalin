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

            $post = Post::create([
                'user_id'      => $admin->id,
                'title'        => $title,
                'editorial'    => $faker->optional(0.7)->sentence(),
                'content'      => '<p>' . implode('</p><p>', $paragraphs) . '</p>',
                'content_mode' => ContentFormat::Html->value,
                'private'      => $isPrivate ? PostStatus::Private->value : PostStatus::Public->value,
                'created_at'   => $createdAt->toDateTimeString(),
                'updated_at'   => $createdAt->toDateTimeString(),
            ]);

            // Two of three posts get a cover so the homepage shows both covers and placeholders
            if ($index % 3 !== 2) {
                $this->attachCover($post, $index);
            }
        }
    }

    /**
     * Generates an orienteering-map-like cover (ISOM colours: yellow open land, green vegetation,
     * blue water, brown contours) and attaches it to the post's cover collection.
     * Keeps the repo free of binary demo photos.
     */
    private function attachCover(Post $post, int $seed): void
    {
        mt_srand($seed);

        // Rendered at half size and upscaled: pixel-by-pixel PHP is slow, and the upscale
        // also thickens contours so they stay visible on the small card conversion
        $width = 840;
        $height = 240;
        $image = imagecreatetruecolor($width, $height);

        $colors = [
            'forest'  => (int) imagecolorallocate($image, 255, 255, 255),
            'open'    => (int) imagecolorallocate($image, 255, 214, 110),
            'green'   => (int) imagecolorallocate($image, 150, 214, 140),
            'water'   => (int) imagecolorallocate($image, 140, 205, 240),
            'contour' => (int) imagecolorallocate($image, 190, 105, 40),
        ];

        $elevation = $this->randomHills(6, $width, $height, 190.0);
        $vegetation = $this->randomHills(5, $width, $height, 130.0);
        $contourStep = 0.22;
        $previousRow = [];

        for ($y = 0; $y < $height; $y++) {
            $row = [];

            for ($x = 0; $x < $width; $x++) {
                $h = $this->surface($elevation, $x, $y);
                $row[$x] = (int) floor($h / $contourStep);

                // A contour crosses between this pixel and its left or upper neighbour
                $isContour = ($x > 0 && $row[$x] !== $row[$x - 1])
                    || ($y > 0 && $row[$x] !== $previousRow[$x]);

                if ($isContour && $h >= 0.12) {
                    $color = $colors['contour'];
                } elseif ($h < 0.12) {
                    $color = $colors['water'];
                } else {
                    $v = $this->surface($vegetation, $x, $y);
                    $color = match (true) {
                        $v > 0.9 => $colors['green'],
                        $v < 0.25 => $colors['open'],
                        default => $colors['forest'],
                    };
                }

                imagesetpixel($image, $x, $y, $color);
            }

            $previousRow = $row;
        }

        $upscaled = imagescale($image, $width * 2, $height * 2);
        imagedestroy($image);

        if ($upscaled === false) {
            return;
        }

        $tempBase = (string) tempnam(sys_get_temp_dir(), 'demo-cover');
        $path = $tempBase . '.jpg';
        imagejpeg($upscaled, $path, 88);
        imagedestroy($upscaled);

        $post->addMedia($path)
            ->usingFileName('novinka-' . $post->id . '.jpg')
            ->toMediaCollection(Post::MEDIA_COLLECTION_COVER);

        unlink($tempBase);
    }

    /**
     * @return list<array{x: int, y: int, height: float, spread: int}>
     */
    private function randomHills(int $count, int $width, int $height, float $maxSpread): array
    {
        $hills = [];

        for ($i = 0; $i < $count; $i++) {
            $hills[] = [
                'x'      => mt_rand(-50, $width + 50),
                'y'      => mt_rand(-50, $height + 50),
                'height' => mt_rand(40, 100) / 100.0,
                'spread' => mt_rand((int) ($maxSpread * 0.4), (int) $maxSpread),
            ];
        }

        return $hills;
    }

    /**
     * Sum of gaussian hills at the given point.
     *
     * @param list<array{x: int, y: int, height: float, spread: int}> $hills
     */
    private function surface(array $hills, int $x, int $y): float
    {
        $value = 0.0;

        foreach ($hills as $hill) {
            $dx = $x - $hill['x'];
            $dy = $y - $hill['y'];
            $value += $hill['height'] * exp(-($dx * $dx + $dy * $dy) / (2 * $hill['spread'] ** 2));
        }

        return $value;
    }
}
