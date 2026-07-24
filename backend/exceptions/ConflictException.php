<?php

declare(strict_types=1);

final class ConflictException extends HttpException
{
    public function getStatusCode(): int
    {
        return 409;
    }
}
