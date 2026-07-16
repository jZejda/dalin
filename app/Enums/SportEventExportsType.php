<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SportEventExportsType: string implements HasColor, HasIcon, HasLabel
{
    case EventEntryListCat = 'eventEntryListCat';
    case ResultEntryListCat = 'resultEntryListCat';

    public function getLabel(): ?string
    {
        $trKey = 'sport-event-export.type_enum.';

        return match ($this) {
            self::EventEntryListCat => __($trKey.self::EventEntryListCat->value),
            self::ResultEntryListCat => __($trKey.self::ResultEntryListCat->value),
        };
    }

    public function getColor(): string|array|null
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
        if ($title !== null) {
            return $title;
        }

        $trKey = 'sport-event-export.aside_link_title_enum.';

        return match ($this) {
            self::EventEntryListCat => __($trKey.self::EventEntryListCat->value),
            self::ResultEntryListCat => __($trKey.self::ResultEntryListCat->value),
        };
    }

    public function getUrlPart(): string
    {
        return match ($this) {
            self::EventEntryListCat => 'startovka',
            self::ResultEntryListCat => 'vysledky',
        };
    }
}
