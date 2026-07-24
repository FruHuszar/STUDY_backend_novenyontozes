<?php

declare(strict_types=1);

final class PlantModel
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $nickname,
        private readonly ?string $location,
        private readonly ?int $wateringIntervalHours,
        private readonly ?string $nextWatering,
        private readonly bool $needsAttention,
        private readonly ?string $note,
        private readonly int $userId,
        private readonly int $speciesId
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['nickname'],
            $row['location'] !== null ? (string) $row['location'] : null,
            $row['watering_interval_hours'] !== null ? (int) $row['watering_interval_hours'] : null,
            (string) $row['next_watering'],
            (bool) $row['needs_attention'],
            $row['note'] !== null ? (string) $row['note'] : null,
            (int) $row['user_id'],
            (int) $row['species_id']
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getWateringIntervalHours(): ?int
    {
        return $this->wateringIntervalHours;
    }

    public function getNextWatering(): ?string
    {
        return $this->nextWatering;
    }

    public function needsAttention(): bool
    {
        return $this->needsAttention;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSpeciesId(): int
    {
        return $this->speciesId;
    }
}
