<?php

declare(strict_types=1);

final class ValidationException extends HttpException
{
    public static function withErrors(array $errors): self
    {
        return new self('The request contains invalid fields.', $errors);
    }

    public static function field(string $field, string $message): self
    {
        return self::withErrors([$field => $message]);
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
