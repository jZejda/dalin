<?php

namespace App\Http\Components\Oris\Shared;

class BaseResponse
{
    private string $Method;
    private string $Format;
    private string $Status;
    private string $ExportCreated;

    public function __construct(string $Method, string $Format, string $Status, string $ExportCreated)
    {
        $this->Method = $Method;
        $this->Format = $Format;
        $this->Status = $Status;
        $this->ExportCreated = $ExportCreated;
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
}
