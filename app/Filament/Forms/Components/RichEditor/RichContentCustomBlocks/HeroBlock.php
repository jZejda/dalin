<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class HeroBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getLabel(): string
    {
        return 'Hero';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-photo';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Konfigurace Hero bloku')
            ->modalDescription('Nastavte parametry pro hero sekci')
            ->schema([
                TextInput::make('heading')
                    ->label('Hlavní nadpis')
                    ->required()
                    ->placeholder('Zadejte hlavní nadpis')
                    ->maxLength(100),
                TextInput::make('subheading')
                    ->label('Podnadpis')
                    ->placeholder('Zadejte podnadpis (volitelné)')
                    ->maxLength(200),
                TextInput::make('buttonLabel')
                    ->label('Text tlačítka')
                    ->placeholder('Zadejte text tlačítka (volitelné)')
                    ->maxLength(50),
                TextInput::make('buttonUrl')
                    ->label('URL tlačítka')
                    ->placeholder('Zadejte URL tlačítka (volitelné)')
                    ->url()
                    ->maxLength(255),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.hero.preview', [
            'heading' => $config['heading'] ?? 'Nepojmenovaný hero blok',
            'subheading' => $config['subheading'] ?? '',
        ])->render();
    }

     /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.hero.index', [
            'heading' => $config['heading'] ?? '',
            'subheading' => $config['subheading'] ?? '',
            'buttonLabel' => $config['buttonLabel'] ?? '',
            'buttonUrl' => $config['buttonUrl'] ?? '',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        return "Hero section: {$config['heading']}";
    }
}
