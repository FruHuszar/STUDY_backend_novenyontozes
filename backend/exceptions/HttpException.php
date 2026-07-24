<?php

declare(strict_types=1);

abstract class HttpException extends RuntimeException
{
    public function __construct(string $message, private readonly array $details = [])
    {
        parent::__construct($message);
    }

    abstract public function getStatusCode(): int;

    public function getDetails(): array
    {
        return $this->details;
    }
}
