<?php

declare(strict_types=1);

final class Env
{
    public static function load(string $path): void
    {
        if (!is_readable($path)) {
            throw new RuntimeException("The .env file is not readable: {$path}");
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));

            $value = trim($value, "\"'");

            $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? getenv($key);

        return is_string($value) && $value !== '' ? $value : $default;
    }
}
