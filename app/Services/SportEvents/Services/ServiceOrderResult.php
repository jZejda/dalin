<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

use App\Models\SportServiceOrder;

readonly class ServiceOrderResult
{
    public function __construct(
        public bool $success,
        public ?SportServiceOrder $order = null,
        public ?string $error = null,
    ) {
    }

    public static function failure(string $error): self
    {
        return new self(success: false, error: $error);
    }
}
