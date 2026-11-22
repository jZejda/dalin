<?php

declare(strict_types=1);

namespace App\Services\SiChips;

use App\Enums\SiChipSport;
use App\Enums\SiChipType;

/**
 * One SI chip with the sport and type it is valid for (the same shape in Dalin and ORIS).
 */
final readonly class SiChipData
{
    public function __construct(
        public int $si,
        public SiChipSport $sport = SiChipSport::All,
        public SiChipType $type = SiChipType::All,
    ) {
    }

    public function key(): string
    {
        return $this->si.'-'.$this->sport->value.'-'.$this->type->value;
    }

    public function label(): string
    {
        $parts = [(string) $this->si];

        if ($this->sport !== SiChipSport::All) {
            $parts[] = $this->sport->getLabel();
        }

        if ($this->type !== SiChipType::All) {
            $parts[] = $this->type->getLabel();
        }

        return implode(' · ', $parts);
    }
}
