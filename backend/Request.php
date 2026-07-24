<?php

declare(strict_types=1);

final class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $body;

    private function __construct(string $method, string $path, array $query, array $body)
    {
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
    }

    public static function fromGlobals(): self
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        if ($base !== '/' && $base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $decoded = json_decode(file_get_contents('php://input') ?: '', true);

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            '/' . trim($uri, '/'),
            $_GET,
            is_array($decoded) ? $decoded : []
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function query(string $key, ?string $default = null): ?string
    {
        $value = $this->query[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $default;
    }

    public function queryInt(string $key): ?int
    {
        $value = $this->query($key);

        return $value !== null && ctype_digit($value) ? (int) $value : null;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->body);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }
}
