<?php

namespace App\Http\Components\OpenMap\Response;

class BaseResponse
{
    private string $cod; // "200" OK
    private string $message;
    private int $cnt;
    /** @var ListResponse[] $list */
    private array $list;

    public function __construct(string $cod, string $message, int $cnt, array $list)
    {
        $this->cod = $cod;
        $this->message = $message;
        $this->cnt = $cnt;
        $this->list = $list;
    }

    public function getCod(): string
    {
        return $this->cod;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCnt(): int
    {
        return $this->cnt;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
