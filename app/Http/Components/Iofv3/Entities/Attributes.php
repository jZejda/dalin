<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

final class Attributes
{
    private string $iofVersion;
    private string $createTime;
    private string $creator;

    public function __construct(string $iofVersion, string $createTime, string $creator)
    {
        $this->iofVersion = $iofVersion;
        $this->createTime = $createTime;
        $this->creator = $creator;
    }

    public function getIofVersion(): string
    {
        return $this->iofVersion;
    }

    public function getCreateTime(): string
    {
        return $this->createTime;
    }

    public function getCreator(): string
    {
        return $this->creator;
    }
}
