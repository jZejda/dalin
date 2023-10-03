<?php

declare(strict_types=1);

namespace App\Shared\Entities;

readonly class FrontendLinks
{
    public function __construct(
        public string $type,
        public string $title,
        public string $url,
    ) {}
}
