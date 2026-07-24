<?php

declare(strict_types=1);

final class MyPlantModel implements JsonSerializable
{
    private ?int $id;
    private string $nickname;
    private ?string $location;
    private ?int $wateringIntervalHours;
    private string $nextWatering;
    private bool $needsAttention;
    private ?string $note;
    private int $userId;
    private int $speciesId;
    private ?SpeciesModel $species;

    public function __construct(
        ?int $id,
        string $nickname,
        ?string $location,
        ?int $wateringIntervalHours,
        string $nextWatering,
        bool $needsAttention,
        ?string $note,
        int $userId,
        int $speciesId,
        ?SpeciesModel $species = null
    ) {
        $this->id = $id;
        $this->nickname = $nickname;
        $this->location = $location;
        $this->wateringIntervalHours = $wateringIntervalHours;
        $this->nextWatering = $nextWatering;
        $this->needsAttention = $needsAttention;
        $this->note = $note;
        $this->userId = $userId;
        $this->speciesId = $speciesId;
        $this->species = $species;
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

    public function getWateringIntervalHours(): ?int
    {
        return $this->wateringIntervalHours;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSpeciesId(): int
    {
        return $this->speciesId;
    }

    public function withSpecies(SpeciesModel $species): self
    {
        return new self(
            $this->id,
            $this->nickname,
            $this->location,
            $this->wateringIntervalHours,
            $this->nextWatering,
            $this->needsAttention,
            $this->note,
            $this->userId,
            $this->speciesId,
            $species
        );
    }

    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'name' => $this->nickname,
            'location' => $this->location,
            'nextWatering' => str_replace(' ', 'T', $this->nextWatering),
            'needsAttention' => $this->needsAttention,
            'note' => $this->note,
            'userId' => $this->userId,
            'speciesId' => $this->speciesId,
            'wateringIntervalHours' => $this->wateringIntervalHours,
        ];

        if ($this->species === null) {
            return $payload;
        }

        $species = $this->species->jsonSerialize();

        return array_merge($payload, [
            'latin' => $species['latin'],
            'img' => $species['img'],
            'facts' => $species['facts'],
            'phases' => $species['phases'],
            'note' => $this->note ?? $species['note'],
            'wateringIntervalHours' => $this->wateringIntervalHours ?? $species['wateringIntervalHours'],
        ]);
    }
}
