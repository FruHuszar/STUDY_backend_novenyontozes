<?php

declare(strict_types=1);

final class NotFoundException extends HttpException
{
    public static function resource(string $name): self
    {
        return new self("{$name} not found.");
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
