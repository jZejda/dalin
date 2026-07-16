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
        return __('rich-editor.blocks.alert.label');
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-information-circle';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading(__('rich-editor.blocks.alert.modal_heading'))
            ->modalDescription(__('rich-editor.blocks.alert.modal_description'))
            ->schema([
                Select::make('type')
                    ->label(__('rich-editor.blocks.alert.type'))
                    ->options([
                        'default' => __('rich-editor.blocks.alert.type_options.default'),
                        'info' => __('rich-editor.blocks.alert.type_options.info'),
                        'warning' => __('rich-editor.blocks.alert.type_options.warning'),
                        'error' => __('rich-editor.blocks.alert.type_options.error'),
                    ])
                    ->default('default')
                    ->required(),
                TextInput::make('heading')
                    ->label(__('rich-editor.blocks.alert.heading'))
                    ->required()
                    ->placeholder(__('rich-editor.blocks.alert.heading_placeholder'))
                    ->maxLength(100),
                MarkdownEditor::make('content')
                    ->label(__('rich-editor.blocks.alert.content'))
                    ->placeholder(__('rich-editor.blocks.alert.content_placeholder'))
                    ->maxLength(1000),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.alert.preview', [
            'type' => $config['type'] ?? 'default',
            'heading' => $config['heading'] ?? __('rich-editor.blocks.alert.preview_untitled'),
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
            'default' => __('rich-editor.blocks.alert.type_options.default'),
            'info' => __('rich-editor.blocks.alert.type_options.info'),
            'warning' => __('rich-editor.blocks.alert.type_options.warning'),
            'error' => __('rich-editor.blocks.alert.type_options.error'),
        ];

        $type = $config['type'] ?? 'default';
        $type = is_string($type) ? $type : 'default';
        $typeLabel = $typeLabels[$type] ?? $typeLabels['default'];
        $heading = $config['heading'] ?? __('rich-editor.blocks.alert.preview_untitled');

        return __('rich-editor.blocks.alert.preview_label', [
            'type' => $typeLabel,
            'heading' => (string) $heading,
        ]);
    }
}
