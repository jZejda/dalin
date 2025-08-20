<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class Transaction
{
    public function __construct(
        public ?Column $column0,
        public ?Column $column1,
        public ?Column $column2,
        public ?Column $column3,
        public ?Column $column4,
        public ?Column $column5,
        public ?Column $column10,
        public ?Column $column12,
        public ?Column $column14,
        public ?Column $column16,
        public ?Column $column22,
    ) {
    }
}
