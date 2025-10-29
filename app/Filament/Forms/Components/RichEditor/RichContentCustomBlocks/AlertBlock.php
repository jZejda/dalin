<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class AlertBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'alert';
    }

    public static function getLabel(): string
    {
        return 'Alert';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-information-circle';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Konfigurace Alert bloku')
            ->modalDescription('Nastavte parametry pro alert sekci')
            ->schema([
                Select::make('type')
                    ->label('Typ alertu')
                    ->options([
                        'default' => 'Výchozí',
                        'info' => 'Informace',
                        'warning' => 'Varování',
                        'error' => 'Chyba',
                    ])
                    ->default('default')
                    ->required(),
                TextInput::make('heading')
                    ->label('Nadpis')
                    ->required()
                    ->placeholder('Zadejte nadpis alertu')
                    ->maxLength(100),
                MarkdownEditor::make('content')
                    ->label('Obsah (Markdown)')
                    ->placeholder('Zadejte obsah v Markdown formátu')
                    ->maxLength(1000),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.alert.preview', [
            'type' => $config['type'] ?? 'default',
            'heading' => $config['heading'] ?? 'Nepojmenovaný alert',
            'content' => $config['content'] ?? '',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.alert.index', [
            'type' => $config['type'] ?? 'default',
            'heading' => $config['heading'] ?? '',
            'content' => $config['content'] ?? '',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $typeLabels = [
            'default' => 'Výchozí',
            'info' => 'Informace',
            'warning' => 'Varování',
            'error' => 'Chyba',
        ];

        $typeLabel = $typeLabels[$config['type'] ?? 'default'] ?? 'Výchozí';
        $heading = $config['heading'] ?? 'Nepojmenovaný alert';

        return "Alert ({$typeLabel}): {$heading}";
    }
}
