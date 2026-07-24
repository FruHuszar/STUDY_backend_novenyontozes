<?php

declare(strict_types=1);

final class UserModel implements JsonSerializable
{
    private ?int $id;
    private string $email;
    private string $passwordHash;
    private string $name;
    private bool $notifyEmail;
    private bool $notifyPush;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        string $email,
        string $passwordHash,
        string $name,
        bool $notifyEmail,
        bool $notifyPush,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->name = $name;
        $this->notifyEmail = $notifyEmail;
        $this->notifyPush = $notifyPush;
        $this->createdAt = $createdAt;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['email'],
            (string) $row['password_hash'],
            (string) $row['name'],
            (bool) $row['notify_email'],
            (bool) $row['notify_push'],
            (string) $row['created_at']
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNotifyEmail(): bool
    {
        return $this->notifyEmail;
    }

    public function isNotifyPush(): bool
    {
        return $this->notifyPush;
    }

    public function withId(int $id): self
    {
        return new self($id, $this->email, $this->passwordHash, $this->name, $this->notifyEmail, $this->notifyPush, $this->createdAt);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'notifyEmail' => $this->notifyEmail,
            'notifyPush' => $this->notifyPush,
            'createdAt' => $this->createdAt,
        ];
    }
}
