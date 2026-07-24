<?php

declare(strict_types=1);

final class Response
{
    public static function json(int $status, mixed $payload): void
    {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function created(string $location, mixed $payload): void
    {
        header('Location: ' . $location);
        self::json(201, $payload);
    }

    public static function noContent(): void
    {
        http_response_code(204);
    }

    public static function error(int $status, string $message, array $details = []): void
    {
        $payload = ['error' => $message];

        if ($details !== []) {
            $payload['details'] = $details;
        }

        self::json($status, $payload);
    }
}
