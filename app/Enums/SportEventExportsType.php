<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum SportEventExportsType: string implements HasLabel, HasColor, HasIcon
{
    case EventEntryListCat = 'eventEntryListCat';
    case ResultEntryListCat = 'resultEntryListCat';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EventEntryListCat => 'Startovka kategorie',
            self::ResultEntryListCat => 'Výsledky kategorie',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::EventEntryListCat => 'success',
            self::ResultEntryListCat => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EventEntryListCat => 'heroicon-o-clipboard-document-list',
            self::ResultEntryListCat => 'heroicon-s-flag',
        };
    }

    public function getAsideLinkTitle(?string $title = null): string
    {
        return match($this) {
            self::EventEntryListCat => $title === null ? 'Startovka' : $title,
            self::ResultEntryListCat => $title === null ? 'Výsledky' : $title,
        };
    }

    public function getUrlPart(): string
    {
        return match($this) {
            self::EventEntryListCat => 'startovka',
            self::ResultEntryListCat => 'vysledky',
        };
    }
}
