<?php

namespace App\Services;

class SystemSyncDetail
{
    private string $status;
    private ?array $newItems;
    private ?array $updatedItems;

    public function __construct(string $status, ?array $newItems, ?array $updatedItems)
    {
        $this->status = $status;
        $this->newItems = $newItems;
        $this->updatedItems = $updatedItems;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNewItems(): ?array
    {
        return $this->newItems;
    }

    public function getUpdatedItems(): ?array
    {
        return $this->updatedItems;
    }
}
