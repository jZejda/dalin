<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response;

readonly class OrisUser
{
    public function __construct(
        public string $ID,
        public string $FirstName,
        public string $LastName,
        public ?string $RefLicenceOB,
    ) {
    }
}
