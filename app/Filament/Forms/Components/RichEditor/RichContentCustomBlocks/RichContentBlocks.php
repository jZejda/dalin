<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

final class RichContentBlocks
{
    /**
     * Jediný zdroj pravdy pro RichEditor custom bloky.
     * Používá se v PageResource (editor) i ve frontend views (RichContentRenderer).
     *
     * @return list<class-string<RichContentCustomBlock>>
     */
    public static function all(): array
    {
        return [
            HeroBlock::class,
            AlertBlock::class,
            TableBlock::class,
            ContentDividerBlock::class,
            SimpleDividerBlock::class,
        ];
    }
}
