<?php

namespace App\Services;

use Exception;

class ApiStoreResponseException extends Exception
{
    protected string $model;
    protected ?int $userId;

    public function __construct(string $message = '', string $model = '', ?int $userId = null, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->model = $model;
        $this->userId = $userId;
    }
    public function getStoreError(): string
    {
        $message = 'ORIS-API | ';
        $message .= 'Model: ' . $this->model . ' | ';
        $message .= 'UserId: ' . $this->userId . ' | ';
        $message .= 'Message: ' . $this->message . ' | ';
        $message .= 'Error: ' . $this->getPrevious();

        return $message;
    }
}
