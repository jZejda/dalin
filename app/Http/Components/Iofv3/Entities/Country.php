<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Country
{
    private ?string $code;

    public function __construct(?string $code = null)
    {
        $this->code = $code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
