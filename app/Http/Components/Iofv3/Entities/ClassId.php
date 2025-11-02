<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class ClassId
{
    private string $value {
        get {
            return $this->value;
        }
    }
    private string $type {
        get {
            return $this->type;
        }
    }

    public function __construct(string $value, string $type = 'DALIN')
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
