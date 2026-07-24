<?php

declare(strict_types=1);

final class NotificationModel implements JsonSerializable
{
    private ?int $id;
    private string $type;
    private string $channel;
    private string $message;
    private ?string $sentAt;
    private bool $isRead;
    private ?int $myPlantId;
    private int $userId;

    public function __construct(
        ?int $id,
        string $type,
        string $channel,
        string $message,
        ?string $sentAt,
        bool $isRead,
        ?int $myPlantId,
        int $userId
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->channel = $channel;
        $this->message = $message;
        $this->sentAt = $sentAt;
        $this->isRead = $isRead;
        $this->myPlantId = $myPlantId;
        $this->userId = $userId;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['type'],
            (string) $row['channel'],
            (string) $row['message'],
            $row['sent_at'] !== null ? (string) $row['sent_at'] : null,
            (bool) $row['is_read'],
            $row['my_plant_id'] !== null ? (int) $row['my_plant_id'] : null,
            (int) $row['user_id']
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'channel' => $this->channel,
            'message' => $this->message,
            'sentAt' => $this->sentAt !== null ? str_replace(' ', 'T', $this->sentAt) : null,
            'isRead' => $this->isRead,
            'plantId' => $this->myPlantId,
            'userId' => $this->userId,
        ];
    }
}
