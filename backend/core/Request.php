<?php

declare(strict_types=1);

final class Request
{
    private function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $body,
        private readonly array $parameters = []
    ) {
    }

    public static function fromGlobals(): self
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
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

    public function withParameters(array $parameters): self
    {
        return new self($this->method, $this->path, $this->query, $this->body, $parameters);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function id(string $key = 'id'): int
    {
        $value = $this->parameters[$key] ?? '';

        if (!ctype_digit($value)) {
            throw new NotFoundException('Unknown route.');
        }

        return (int) $value;
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

    public function validator(): Validator
    {
        return Validator::make($this->body);
    }
}
