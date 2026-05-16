<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TableBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'table';
    }

    public static function getLabel(): string
    {
        return 'Tabulka';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-table-cells';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Konfigurace tabulky')
            ->modalDescription('Vlož data oddělená tabulátory (např. zkopírovaná z Excelu, ORIS nebo PDF). Sloupce lze také oddělit dvěma a více mezerami.')
            ->schema([
                TextInput::make('title')
                    ->label('Nadpis (volitelný)')
                    ->maxLength(150),
                Textarea::make('raw')
                    ->label('Data tabulky')
                    ->required()
                    ->rows(12)
                    ->autosize()
                    ->placeholder("kat\tdélka\tpřev.\tkontroly\nD10\t2,6\t65\t9\nD12\t2,8\t65\t10")
                    ->helperText('Každý řádek = jeden řádek tabulky. Sloupce odděl tabulátorem nebo víc mezerami.'),
                Checkbox::make('has_header')
                    ->label('První řádek je hlavička')
                    ->default(true),
                Checkbox::make('striped')
                    ->label('Proužkování řádků')
                    ->default(true),
                Checkbox::make('compact')
                    ->label('Kompaktní (menší padding)')
                    ->default(false),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        $parsed = self::parse((string) ($config['raw'] ?? ''));

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.table.preview', [
            'title' => $config['title'] ?? null,
            'headers' => $parsed['headers'],
            'rows' => $parsed['rows'],
            'hasHeader' => (bool) ($config['has_header'] ?? true),
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        $parsed = self::parse((string) ($config['raw'] ?? ''));

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.table.index', [
            'title' => $config['title'] ?? null,
            'headers' => $parsed['headers'],
            'rows' => $parsed['rows'],
            'hasHeader' => (bool) ($config['has_header'] ?? true),
            'striped' => (bool) ($config['striped'] ?? true),
            'compact' => (bool) ($config['compact'] ?? false),
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $title = trim((string) ($config['title'] ?? ''));
        $parsed = self::parse((string) ($config['raw'] ?? ''));
        $rowCount = count($parsed['rows']) + (! empty($parsed['headers']) ? 1 : 0);

        return $title !== ''
            ? "Tabulka: {$title} ({$rowCount} ř.)"
            : "Tabulka ({$rowCount} ř.)";
    }

    /**
     * Rozparsuje surový text na hlavičku a řádky.
     * Sloupce: tabulátor → 2+ mezery → fallback jednoduchá mezera.
     *
     * @return array{headers: list<string>, rows: list<list<string>>}
     */
    private static function parse(string $raw): array
    {
        $raw = trim($raw);

        if ($raw === '') {
            return ['headers' => [], 'rows' => []];
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $lines = array_values(array_filter($lines, static fn (string $line): bool => trim($line) !== ''));

        if ($lines === []) {
            return ['headers' => [], 'rows' => []];
        }

        $useTab = str_contains($lines[0], "\t");

        $split = static function (string $line) use ($useTab): array {
            if ($useTab) {
                $parts = explode("\t", $line);
            } else {
                $parts = preg_split('/ {2,}|\t/', $line) ?: [];
            }

            return array_values(array_map('trim', $parts));
        };

        $headers = $split($lines[0]);
        $rows = [];
        foreach (array_slice($lines, 1) as $line) {
            $rows[] = $split($line);
        }

        return ['headers' => $headers, 'rows' => $rows];
    }
}
