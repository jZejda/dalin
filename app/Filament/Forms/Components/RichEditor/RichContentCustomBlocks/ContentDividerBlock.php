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
        return __('rich-editor.blocks.content_divider.label');
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-minus';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading(__('rich-editor.blocks.content_divider.modal_heading'))
            ->modalDescription(__('rich-editor.blocks.content_divider.modal_description'))
            ->schema([
                TextInput::make('separator')
                    ->label(__('rich-editor.blocks.content_divider.separator'))
                    ->required()
                    ->placeholder(__('rich-editor.blocks.content_divider.separator_placeholder'))
                    ->maxLength(50),
                TextInput::make('heading')
                    ->label(__('rich-editor.blocks.content_divider.heading'))
                    ->required()
                    ->placeholder(__('rich-editor.blocks.content_divider.heading_placeholder'))
                    ->maxLength(100),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.content-divider.preview', [
            'separator' => $config['separator'] ?? '',
            'heading' => $config['heading'] ?? __('rich-editor.blocks.content_divider.preview_untitled'),
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
        return __('rich-editor.blocks.content_divider.preview_label', ['heading' => (string) ($config['heading'] ?? '')]);
    }
}
