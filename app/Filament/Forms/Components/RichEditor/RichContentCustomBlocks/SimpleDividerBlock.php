<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class SimpleDividerBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'simple-divider';
    }

    public static function getLabel(): string
    {
        return 'Jednoduchý oddělovník';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-stop';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Konfigurace jednoduchého oddělovníku')
            ->modalDescription('Nastavte číslo lampionu a nadpis')
            ->schema([
                TextInput::make('number')
                    ->label('Číslo lampionu')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(999)
                    ->placeholder('Např. 31'),
                TextInput::make('heading')
                    ->label('Nadpis')
                    ->required()
                    ->placeholder('Zadejte nadpis')
                    ->maxLength(150),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.simple-divider.preview', [
            'number' => $config['number'] ?? '1',
            'heading' => $config['heading'] ?? 'Nepojmenovaný oddělovník',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.simple-divider.index', [
            'number' => $config['number'] ?? '1',
            'heading' => $config['heading'] ?? '',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        return "Lampion #{$config['number']}: {$config['heading']}";
    }
}
