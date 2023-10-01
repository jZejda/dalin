<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Name
{
    private string $Family;
    private string $Given;

    /**
     * @param string $Family
     * @param string $Given
     */
    public function __construct(string $Family, string $Given)
    {
        $this->Family = $Family;
        $this->Given = $Given;
    }

    public function getFamily(): string
    {
        return $this->Family;
    }

    public function getGiven(): string
    {
        return $this->Given;
    }
}
