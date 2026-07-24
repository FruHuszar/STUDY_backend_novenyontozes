<?php

declare(strict_types=1);

final class Validator
{
    private array $errors = [];

    private function __construct(private readonly array $data)
    {
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function has(string $field): bool
    {
        return array_key_exists($field, $this->data);
    }

    public function requiredString(string $field, int $maxLength = 255): string
    {
        $value = $this->data[$field] ?? null;

        if (!is_string($value) || trim($value) === '') {
            return $this->fail($field, 'This field is required.', '');
        }

        if (mb_strlen($value) > $maxLength) {
            return $this->fail($field, "This field may not be longer than {$maxLength} characters.", '');
        }

        return trim($value);
    }

    public function optionalString(string $field, int $maxLength = 255): ?string
    {
        $value = $this->data[$field] ?? null;

        if ($value === null || (is_string($value) && trim($value) === '')) {
            return null;
        }

        return $this->requiredString($field, $maxLength);
    }

    public function requiredEmail(string $field): string
    {
        $value = $this->requiredString($field, 150);

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return $this->fail($field, 'This field must be a valid email address.', '');
        }

        return $value;
    }

    public function requiredPassword(string $field, int $minLength = 8): string
    {
        $value = $this->data[$field] ?? null;

        if (!is_string($value) || mb_strlen($value) < $minLength) {
            return $this->fail($field, "This field must be at least {$minLength} characters long.", '');
        }

        return $value;
    }

    public function requiredId(string $field): int
    {
        $value = $this->data[$field] ?? null;

        if (!is_numeric($value) || (int) $value < 1) {
            return $this->fail($field, 'This field must be a valid identifier.', 0);
        }

        return (int) $value;
    }

    public function optionalPositiveInt(string $field): ?int
    {
        $value = $this->data[$field] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_numeric($value) || (int) $value < 1) {
            return $this->fail($field, 'This field must be a positive number.', null);
        }

        return (int) $value;
    }

    public function optionalNonNegativeInt(string $field): ?int
    {
        $value = $this->data[$field] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_numeric($value) || (int) $value < 0) {
            return $this->fail($field, 'This field must be a non-negative number.', null);
        }

        return (int) $value;
    }

    public function boolean(string $field, bool $default = false): bool
    {
        if (!$this->has($field)) {
            return $default;
        }

        $value = filter_var($this->data[$field], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        if ($value === null) {
            return $this->fail($field, 'This field must be true or false.', $default);
        }

        return $value;
    }

    public function enum(string $field, array $allowed, string $default): string
    {
        if (!$this->has($field) || $this->data[$field] === null) {
            return $default;
        }

        $value = $this->data[$field];

        if (!in_array($value, $allowed, true)) {
            return $this->fail($field, 'This field must be one of: ' . implode(', ', $allowed) . '.', $default);
        }

        return $value;
    }

    public function assertValid(): void
    {
        if ($this->errors !== []) {
            throw ValidationException::withErrors($this->errors);
        }
    }

    private function fail(string $field, string $message, mixed $fallback): mixed
    {
        $this->errors[$field] = $message;

        return $fallback;
    }
}
