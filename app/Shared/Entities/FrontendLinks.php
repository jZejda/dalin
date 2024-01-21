<?php

declare(strict_types=1);

namespace App\Shared\Entities;

use App\Enums\SportEventExportsType;

readonly class FrontendLinks
{
    public function __construct(
        public SportEventExportsType $type,
        public string $title,
        public string $url,
    ) {
    }
}
