<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * Recursively collects all PHP language files below lang/{locale}, excluding
 * lang/vendor (vendor translations are out of scope for CS/EN parity).
 *
 * @return array<string, string> map of relative path (e.g. "mail/common.php") => absolute path
 */
function langPhpFiles(string $locale): array
{
    $base = base_path("lang/{$locale}");
    $files = [];

    if (! File::isDirectory($base)) {
        return [];
    }

    foreach (File::allFiles($base) as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $relative = str_replace('\\', '/', $file->getRelativePathname());
        $files[$relative] = $file->getPathname();
    }

    ksort($files);

    return $files;
}

/**
 * Recursively extracts dot-notated key paths from a (possibly nested) translation array.
 *
 * @param array<int|string, mixed> $data
 * @return array<int, string>
 */
function extractLangKeyPaths(array $data, string $prefix = ''): array
{
    $keys = [];

    foreach ($data as $key => $value) {
        $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

        if (is_array($value)) {
            $keys = [...$keys, ...extractLangKeyPaths($value, $path)];
        } else {
            $keys[] = $path;
        }
    }

    return $keys;
}

/**
 * Loads a lang PHP file via `require` (so enum-backed array keys, e.g.
 * `AppRoles::SuperAdmin->value`, are resolved) and returns its dot-notated key paths.
 *
 * @throws RuntimeException
 * @return array<int, string>
 */
function loadLangKeyPaths(string $absolutePath): array
{
    /** @var mixed $content */
    $content = require $absolutePath;

    if (! is_array($content)) {
        throw new RuntimeException("Lang file [{$absolutePath}] does not return an array.");
    }

    $keys = extractLangKeyPaths($content);
    sort($keys);

    return $keys;
}

test('cs and en lang directories contain the same set of php files', function () {
    $csFiles = langPhpFiles('cs');
    $enFiles = langPhpFiles('en');

    $onlyInCs = array_values(array_diff(array_keys($csFiles), array_keys($enFiles)));
    $onlyInEn = array_values(array_diff(array_keys($enFiles), array_keys($csFiles)));

    expect($onlyInCs)->toBe([], 'Files present in lang/cs but missing in lang/en: '.implode(', ', $onlyInCs));
    expect($onlyInEn)->toBe([], 'Files present in lang/en but missing in lang/cs: '.implode(', ', $onlyInEn));
});

test('cs and en lang php files have parity of translation keys', function () {
    $csFiles = langPhpFiles('cs');
    $enFiles = langPhpFiles('en');

    $commonRelativePaths = array_values(array_intersect(array_keys($csFiles), array_keys($enFiles)));

    foreach ($commonRelativePaths as $relativePath) {
        $csKeys = loadLangKeyPaths($csFiles[$relativePath]);
        $enKeys = loadLangKeyPaths($enFiles[$relativePath]);

        $missingInEn = array_values(array_diff($csKeys, $enKeys));
        $missingInCs = array_values(array_diff($enKeys, $csKeys));

        expect($missingInEn)->toBe(
            [],
            "lang/en/{$relativePath} is missing keys present in lang/cs/{$relativePath}: ".implode(', ', $missingInEn)
        );
        expect($missingInCs)->toBe(
            [],
            "lang/cs/{$relativePath} is missing keys present in lang/en/{$relativePath}: ".implode(', ', $missingInCs)
        );
    }
});

test('cs.json and en.json have parity of translation keys', function () {
    $csPath = base_path('lang/cs.json');
    $enPath = base_path('lang/en.json');

    if (! File::exists($csPath) || ! File::exists($enPath)) {
        expect(File::exists($csPath))->toBe(File::exists($enPath));

        return;
    }

    /** @var mixed $csDecoded */
    $csDecoded = json_decode(File::get($csPath), true);
    /** @var mixed $enDecoded */
    $enDecoded = json_decode(File::get($enPath), true);

    if (! is_array($csDecoded)) {
        throw new RuntimeException("lang/cs.json does not decode to an array.");
    }

    if (! is_array($enDecoded)) {
        throw new RuntimeException("lang/en.json does not decode to an array.");
    }

    $csKeys = array_values(array_map('strval', array_keys($csDecoded)));
    $enKeys = array_values(array_map('strval', array_keys($enDecoded)));

    sort($csKeys);
    sort($enKeys);

    $missingInEn = array_values(array_diff($csKeys, $enKeys));
    $missingInCs = array_values(array_diff($enKeys, $csKeys));

    expect($missingInEn)->toBe([], 'lang/en.json is missing keys present in lang/cs.json: '.implode(', ', $missingInEn));
    expect($missingInCs)->toBe([], 'lang/cs.json is missing keys present in lang/en.json: '.implode(', ', $missingInCs));
});
