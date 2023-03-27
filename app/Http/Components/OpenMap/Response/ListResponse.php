<?php

declare(strict_types=1);

namespace App\Http\Components\OpenMap\Response;

class ListResponse
{
    private string $dt;
    private string $visibility;

    public function __construct(string $dt, string $visibility)
    {
        $this->dt = $dt;
        $this->visibility = $visibility;
    }

    public function getDt(): string
    {
        return $this->dt;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
