<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class Column
{
    public function __construct(
        public int $id,
        public string $name,
        public string|int|float|null $value,
    ) {
    }
}
