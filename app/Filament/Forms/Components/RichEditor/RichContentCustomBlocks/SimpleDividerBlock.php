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
        return __('rich-editor.blocks.simple_divider.label');
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-stop';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading(__('rich-editor.blocks.simple_divider.modal_heading'))
            ->modalDescription(__('rich-editor.blocks.simple_divider.modal_description'))
            ->schema([
                TextInput::make('number')
                    ->label(__('rich-editor.blocks.simple_divider.number'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(999)
                    ->placeholder(__('rich-editor.blocks.simple_divider.number_placeholder')),
                TextInput::make('heading')
                    ->label(__('rich-editor.blocks.simple_divider.heading'))
                    ->required()
                    ->placeholder(__('rich-editor.blocks.simple_divider.heading_placeholder'))
                    ->maxLength(150),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.simple-divider.preview', [
            'number' => $config['number'] ?? '1',
            'heading' => $config['heading'] ?? __('rich-editor.blocks.simple_divider.preview_untitled'),
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
        return __('rich-editor.blocks.simple_divider.preview_label', [
            'number' => (string) ($config['number'] ?? ''),
            'heading' => (string) ($config['heading'] ?? ''),
        ]);
    }
}
