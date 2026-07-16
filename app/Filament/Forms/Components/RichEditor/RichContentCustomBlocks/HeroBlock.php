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
        return __('rich-editor.blocks.hero.label');
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-photo';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading(__('rich-editor.blocks.hero.modal_heading'))
            ->modalDescription(__('rich-editor.blocks.hero.modal_description'))
            ->schema([
                TextInput::make('heading')
                    ->label(__('rich-editor.blocks.hero.heading'))
                    ->required()
                    ->placeholder(__('rich-editor.blocks.hero.heading_placeholder'))
                    ->maxLength(100),
                TextInput::make('subheading')
                    ->label(__('rich-editor.blocks.hero.subheading'))
                    ->placeholder(__('rich-editor.blocks.hero.subheading_placeholder'))
                    ->maxLength(200),
                TextInput::make('buttonLabel')
                    ->label(__('rich-editor.blocks.hero.button_label'))
                    ->placeholder(__('rich-editor.blocks.hero.button_label_placeholder'))
                    ->maxLength(50),
                TextInput::make('buttonUrl')
                    ->label(__('rich-editor.blocks.hero.button_url'))
                    ->placeholder(__('rich-editor.blocks.hero.button_url_placeholder'))
                    ->url()
                    ->maxLength(255),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.hero.preview', [
            'heading' => $config['heading'] ?? __('rich-editor.blocks.hero.preview_untitled'),
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
        return __('rich-editor.blocks.hero.preview_label', ['heading' => (string) ($config['heading'] ?? '')]);
    }
}
