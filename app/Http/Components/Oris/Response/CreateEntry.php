<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response;

use App\Http\Components\Oris\Response\Entity\EntryData\Data;

class CreateEntry
{
    public string $Method;
    public string $Format;
    public string $Status;
    public string $ExportCreated; //YMD HIS
    public ?Data $data;

    public function __construct(string $Method, string $Format, string $Status, string $ExportCreated, ?Data $data)
    {
        $this->Method = $Method;
        $this->Format = $Format;
        $this->Status = $Status;
        $this->ExportCreated = $ExportCreated;
        $this->data = $data;
    }

    public function getMethod(): string
    {
        return $this->Method;
    }

    public function getFormat(): string
    {
        return $this->Format;
    }

    public function getStatus(): string
    {
        return $this->Status;
    }

    public function getExportCreated(): string
    {
        return $this->ExportCreated;
    }

    public function getData(): ?Data
    {
        return $this->data;
    }
}
