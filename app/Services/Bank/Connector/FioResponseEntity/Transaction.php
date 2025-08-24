<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector\FioResponseEntity;

readonly class Transaction
{
    public function __construct(
        public ?ColumnStringType $column0,
        public ?ColumnFloatVal $column1,
        public ?ColumnStringType $column2,
        public ?ColumnStringType $column3,
        public ?ColumnStringType $column4,
        public ?ColumnStringType $column5,
        public ?ColumnStringType $column6,
        public ?ColumnStringType $column7,
        public ?ColumnStringType $column8,
        public ?ColumnStringType $column9,
        public ?ColumnStringType $column10,
        public ?ColumnStringType $column12,
        public ?ColumnStringType $column14,
        public ?ColumnStringType $column16,
        public ?ColumnIntVal $column22,
        public ?ColumnStringType $column25,
    ) {
    }
}
