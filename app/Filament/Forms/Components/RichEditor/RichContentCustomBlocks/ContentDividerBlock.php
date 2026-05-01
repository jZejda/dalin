<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class ContentDividerBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'content-divider';
    }

    public static function getLabel(): string
    {
        return 'Oddělovník sekce';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-minus';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Konfigurace oddělovníku sekce')
            ->modalDescription('Nastavte parametry pro oddělovník sekce')
            ->schema([
                TextInput::make('separator')
                    ->label('Oddělovník')
                    ->required()
                    ->placeholder('Např. Aktuality, O klubu, Závody...')
                    ->maxLength(50),
                TextInput::make('heading')
                    ->label('Hlavní nadpis')
                    ->required()
                    ->placeholder('Zadejte hlavní nadpis sekce')
                    ->maxLength(100),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.content-divider.preview', [
            'separator' => $config['separator'] ?? '',
            'heading' => $config['heading'] ?? 'Nepojmenovaný oddělovník',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.content-divider.index', [
            'separator' => $config['separator'] ?? '',
            'heading' => $config['heading'] ?? '',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        return "Oddělovník: {$config['heading']}";
    }
}
