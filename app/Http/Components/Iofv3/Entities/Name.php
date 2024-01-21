<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Name
{
    private string|array|null $Family;
    private string|array|null $Given;

    /**
     * @param array|string|null $Family
     * @param array|string|null $Given
     */
    public function __construct(array|string|null $Family, array|string|null $Given)
    {
        $this->Family = $Family;
        $this->Given = $Given;
    }

    public function getFamily(): array|string|null
    {
        return $this->Family;
    }

    public function getGiven(): array|string|null
    {
        return $this->Given;
    }




}
