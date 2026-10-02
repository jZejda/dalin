<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Pages used to keep SEO values in the `meta` JSON column (edited in the admin but never
 * rendered). Title and description move to the laravel-seo `seo` table, which now drives the
 * frontend, admin and API. `meta` itself is left untouched as a backup; keywords and og:image
 * have no counterpart and are not migrated.
 */
return new class () extends Migration {
    private const string PAGE_MORPH_TYPE = 'App\\Models\\Page';

    public function up(): void
    {
        DB::table('pages')
            ->whereNotNull('meta')
            ->orderBy('id')
            ->select(['id', 'meta'])
            ->each(function (object $page): void {
                $meta = json_decode((string) $page->meta, true);

                if (! is_array($meta)) {
                    return;
                }

                $title = $this->firstFilled($meta, ['title', 'og:title']);
                $description = $this->firstFilled($meta, ['description', 'og:description']);

                if ($title === null && $description === null) {
                    return;
                }

                DB::table('seo')->updateOrInsert(
                    ['model_type' => self::PAGE_MORPH_TYPE, 'model_id' => $page->id],
                    [
                        'title' => $title !== null ? mb_substr($title, 0, 255) : null,
                        'description' => $description,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            });
    }

    public function down(): void
    {
        // Nothing to undo: `meta` was never modified, and deleting `seo` rows would also
        // remove values entered in the admin after this migration.
    }

    /**
     * @param  array<mixed>  $meta
     * @param  list<string>  $keys
     */
    private function firstFilled(array $meta, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $meta[$key] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }
};
