<?php

declare(strict_types=1);

final class Response
{
    private function __construct(
        private readonly int $status,
        private readonly mixed $payload,
        private readonly bool $hasBody,
        private readonly array $headers = []
    ) {
    }

    public static function json(int $status, mixed $payload): self
    {
        return new self($status, $payload, true);
    }

    public static function created(string $location, mixed $payload): self
    {
        return new self(201, $payload, true, ['Location' => $location]);
    }

    public static function noContent(): self
    {
        return new self(204, null, false);
    }

    public static function error(int $status, string $message, array $details = []): self
    {
        $payload = ['error' => $message];

        if ($details !== []) {
            $payload['details'] = $details;
        }

        return new self($status, $payload, true);
    }

    public function withHeader(string $name, string $value): self
    {
        return new self($this->status, $this->payload, $this->hasBody, $this->headers + [$name => $value]);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        if ($this->hasBody) {
            echo json_encode($this->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
}
