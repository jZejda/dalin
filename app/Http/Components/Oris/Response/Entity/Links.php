<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response\Entity;

readonly class Links
{
    public function __construct(
        public string $ID,
        public string $Url,
        public ?SourceType $SourceType,
        public string|null $OtherDescCZ,
        public string|null $OtherDescEN,
    ) {
    }
}
