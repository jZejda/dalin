<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Zjistí, na jaké verzi a na jakém sestavení aplikace běží.
 *
 * Verze je commitnutá v config/version.php (bumpuje se při vydání releasu),
 * sestavení se odvozuje z prostředí:
 *
 *  - na serveru ze souboru REVISION, který do release zapisuje Deployer
 *    (plný git SHA nasazeného kódu, mtime = čas nasazení),
 *  - lokálně z .git/HEAD, pokud REVISION neexistuje,
 *  - jinak není sestavení známé (null) — např. archiv bez gitu.
 */
class AppVersionService
{
    private const SHORT_SHA_LENGTH = 7;

    private bool $resolved = false;

    private ?string $revision = null;

    private ?string $revisionFile = null;

    /**
     * @param  string|null  $basePath  kořen aplikace; null = base_path()
     */
    public function __construct(private readonly ?string $basePath = null)
    {
    }

    /**
     * Číslo vydání bez prefixu, např. "13.1.0".
     */
    public function version(): string
    {
        $version = config('version.version');

        return is_string($version) && $version !== '' ? $version : '0.0.0';
    }

    /**
     * Číslo vydání tak, jak se tagují releasy, např. "v13.1.0".
     */
    public function tag(): string
    {
        return 'v'.$this->version();
    }

    /**
     * Plný git SHA nasazeného kódu, nebo null když ho nelze zjistit.
     */
    public function revision(): ?string
    {
        $this->resolve();

        return $this->revision;
    }

    /**
     * Zkrácený git SHA pro zobrazení, např. "7d09353".
     */
    public function build(): ?string
    {
        $revision = $this->revision();

        return $revision === null ? null : substr($revision, 0, self::SHORT_SHA_LENGTH);
    }

    /**
     * Čas nasazení (mtime souboru, ze kterého se sestavení odvodilo).
     */
    public function builtAt(): ?CarbonImmutable
    {
        $this->resolve();

        if ($this->revisionFile === null) {
            return null;
        }

        $timestamp = @filemtime($this->revisionFile);

        return $timestamp === false ? null : CarbonImmutable::createFromTimestamp($timestamp);
    }

    /**
     * Jednořádkový popis pro log, konzoli nebo hlášení chyby.
     */
    public function summary(): string
    {
        $build = $this->build();

        return $build === null ? $this->tag() : $this->tag().' · build '.$build;
    }

    private function resolve(): void
    {
        if ($this->resolved) {
            return;
        }

        $this->resolved = true;

        $revisionFile = $this->path('REVISION');
        $revision = $this->readFirstLine($revisionFile);

        if ($revision !== null) {
            $this->revision = $revision;
            $this->revisionFile = $revisionFile;

            return;
        }

        $this->resolveFromGit();
    }

    /**
     * Čte .git napřímo — na sdíleném hostingu nelze spoléhat na spouštění
     * externích procesů, a `git` tam stejně není.
     */
    private function resolveFromGit(): void
    {
        $headFile = $this->path('.git/HEAD');
        $head = $this->readFirstLine($headFile);

        if ($head === null) {
            return;
        }

        if (! str_starts_with($head, 'ref: ')) {
            // Odpojená HEAD — v souboru je rovnou SHA.
            $this->revision = $head;
            $this->revisionFile = $headFile;

            return;
        }

        $ref = substr($head, strlen('ref: '));

        $refFile = $this->path('.git/'.$ref);
        $revision = $this->readFirstLine($refFile);

        if ($revision !== null) {
            $this->revision = $revision;
            $this->revisionFile = $refFile;

            return;
        }

        // Větev může být zabalená v .git/packed-refs (po `git gc`).
        $packedFile = $this->path('.git/packed-refs');
        $packed = @file($packedFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($packed === false) {
            return;
        }

        foreach ($packed as $line) {
            if (! str_ends_with($line, ' '.$ref)) {
                continue;
            }

            $this->revision = strtok($line, ' ') ?: null;
            $this->revisionFile = $packedFile;

            return;
        }
    }

    private function readFirstLine(string $file): ?string
    {
        if (! is_file($file) || ! is_readable($file)) {
            return null;
        }

        $contents = @file_get_contents($file);

        if ($contents === false) {
            return null;
        }

        $line = trim(strtok($contents, "\n") ?: '');

        return $line === '' ? null : $line;
    }

    private function path(string $relative): string
    {
        $base = $this->basePath ?? base_path();

        return rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$relative;
    }
}
