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
        return __('rich-editor.blocks.table.label');
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-table-cells';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading(__('rich-editor.blocks.table.modal_heading'))
            ->modalDescription(__('rich-editor.blocks.table.modal_description'))
            ->schema([
                TextInput::make('title')
                    ->label(__('rich-editor.blocks.table.title'))
                    ->maxLength(150),
                Textarea::make('raw')
                    ->label(__('rich-editor.blocks.table.raw'))
                    ->required()
                    ->rows(12)
                    ->autosize()
                    ->placeholder(__('rich-editor.blocks.table.raw_placeholder'))
                    ->helperText(__('rich-editor.blocks.table.raw_helper')),
                Checkbox::make('has_header')
                    ->label(__('rich-editor.blocks.table.has_header'))
                    ->default(true),
                Checkbox::make('striped')
                    ->label(__('rich-editor.blocks.table.striped'))
                    ->default(true),
                Checkbox::make('compact')
                    ->label(__('rich-editor.blocks.table.compact'))
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

        if ($title !== '') {
            return __('rich-editor.blocks.table.preview_label_titled', ['title' => $title, 'count' => $rowCount]);
        }

        return __('rich-editor.blocks.table.preview_label_untitled', ['count' => $rowCount]);
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
