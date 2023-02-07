<?php

declare(strict_types=1);

namespace App\Services;

class OrisResponse
{
    protected ?array $newItem;
    protected ?array $updatedItem;
    protected string $status;

    public function __construct(?array $newItem = [], ?array $updatedItem = [], string $status = 'OK')
    {
        $this->newItem = $newItem;
        $this->updatedItem = $updatedItem;
        $this->status = $status;
    }

    public function getItemsInfo(): SystemSyncDetail
    {
        return new SystemSyncDetail(
            status: $this->status,
            newItems: $this->newItem,
            updatedItems: $this->updatedItem,
        );
    }

    public function setStatus(string $status): string
    {
        $this->status = $status;
        return $this->status;
    }

    public function newItem(string $itemName): array
    {
        $this->newItem[] = $itemName;
        return $this->newItem;
    }

    public function updatedItem(string $itemName): array
    {
        $this->updatedItem[] = $itemName;
        return $this->updatedItem;
    }
}
