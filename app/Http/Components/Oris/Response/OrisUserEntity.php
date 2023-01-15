<?php

declare(strict_types=1);

namespace App\Http\Components\Oris\Response;

class OrisUserEntity
{

    private int $userId;
    private int $id;
    private string $title;
    private string $body;

    /**
     * @param int $userId
     * @param int $id
     * @param string $title
     * @param string $body
     */
    public function __construct(int $userId, int $id, string $title, string $body)
    {
        $this->userId = $userId;
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
