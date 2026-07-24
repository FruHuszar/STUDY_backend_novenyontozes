<?php

declare(strict_types=1);

final class SpeciesModel
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $latinName,
        private readonly ?string $imageUrl,
        private readonly ?string $habitat,
        private readonly ?string $lightNeed,
        private readonly int $wateringIntervalHours,
        private readonly ?string $description,
        private readonly int $familyId,
        private readonly ?string $familyName = null,
        private readonly array $phases = []
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['latin_name'],
            $row['image_url'] !== null ? (string) $row['image_url'] : null,
            $row['habitat'] !== null ? (string) $row['habitat'] : null,
            $row['light_need'] !== null ? (string) $row['light_need'] : null,
            (int) $row['watering_interval_hours'],
            $row['description'] !== null ? (string) $row['description'] : null,
            (int) $row['family_id'],
            isset($row['family_name']) ? (string) $row['family_name'] : null
        );
    }

    public function withPhases(array $phases): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->latinName,
            $this->imageUrl,
            $this->habitat,
            $this->lightNeed,
            $this->wateringIntervalHours,
            $this->description,
            $this->familyId,
            $this->familyName,
            $phases
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLatinName(): string
    {
        return $this->latinName;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getHabitat(): ?string
    {
        return $this->habitat;
    }

    public function getLightNeed(): ?string
    {
        return $this->lightNeed;
    }

    public function getWateringIntervalHours(): int
    {
        return $this->wateringIntervalHours;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFamilyId(): int
    {
        return $this->familyId;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function getPhases(): array
    {
        return $this->phases;
    }
}
